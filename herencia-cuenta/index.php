<?php

// Cargamos las clases antes de recuperar los objetos guardados en la sesión.
require_once __DIR__ . '/clases/CuentaAhorros.php';
require_once __DIR__ . '/clases/CuentaCorriente.php';
date_default_timezone_set('America/Bogota');
session_start();

// Las cuentas se conservan mientras esté activa esta sesión del navegador.
if (!isset($_SESSION['cuentas'])) {
    $_SESSION['cuentas'] = [];
}
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$cuentas = &$_SESSION['cuentas'];

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function dinero(float $valor): string
{
    return ($valor < 0 ? '-$' : '$') . number_format(abs($valor), 2, ',', '.');
}

function leerTexto(array $datos, string $campo): string
{
    if (!isset($datos[$campo]) || !is_string($datos[$campo])) {
        throw new InvalidArgumentException('Falta un dato del formulario. Completa los campos.');
    }
    return trim($datos[$campo]);
}

function leerNumeroCuenta(array $datos, string $campo): int
{
    $texto = leerTexto($datos, $campo);
    // Hasta 9 cifras: sigue siendo un número, incluso en PHP de 32 bits.
    if (!preg_match('/^[1-9][0-9]{0,8}$/D', $texto)) {
        throw new InvalidArgumentException('El número de cuenta debe tener de 1 a 9 cifras, sin ceros al inicio.');
    }
    return (int) $texto;
}

function leerDecimal(array $datos, string $campo): float
{
    $texto = leerTexto($datos, $campo);
    if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/D', $texto)) {
        throw new InvalidArgumentException('Escribe un valor sin separadores de miles y con máximo dos decimales.');
    }
    return (float) $texto;
}

// POST realiza operaciones; la redirección evita repetirlas al recargar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seleccion = null;
    try {
        $token = leerTexto($_POST, 'token');
        if (!hash_equals($_SESSION['token'], $token)) {
            throw new DomainException('El formulario venció. Recarga la página y vuelve a intentarlo.');
        }
        $accion = leerTexto($_POST, 'accion');

        if ($accion === 'crear') {
            // Guardamos los campos para recuperarlos si hay un error.
            $_SESSION['formulario'] = array_intersect_key($_POST, array_flip([
                'numero', 'nombre', 'saldo', 'tipo', 'interes',
            ]));
            $numero = leerNumeroCuenta($_POST, 'numero');
            $nombre = leerTexto($_POST, 'nombre');
            $saldo = leerDecimal($_POST, 'saldo');
            $tipo = leerTexto($_POST, 'tipo');
            if (isset($cuentas[$numero])) {
                throw new DomainException('Ya existe ese número de cuenta. Escribe uno diferente.');
            }

            // Aquí creamos los objetos de las clases hijas con "new".
            if ($tipo === 'ahorros') {
                $interes = leerDecimal($_POST, 'interes');
                $cuenta = new CuentaAhorros($numero, $nombre, $saldo, $interes);
            } elseif ($tipo === 'corriente') {
                $cuenta = new CuentaCorriente($numero, $nombre, $saldo);
            } else {
                throw new InvalidArgumentException('Selecciona ahorros o corriente.');
            }

            $cuentas[$numero] = $cuenta;
            $seleccion = $numero;
            unset($_SESSION['formulario']);
            $mensaje = 'Cuenta de ' . strtolower($cuenta->getTipo()) . ' creada correctamente.';
        } else {
            $seleccion = leerNumeroCuenta($_POST, 'cuenta');
            if (!isset($cuentas[$seleccion])) {
                throw new DomainException('La cuenta no existe. Selecciona una de la lista.');
            }
            $cuenta = $cuentas[$seleccion];

            if ($accion === 'depositar' || $accion === 'retirar') {
                $monto = leerDecimal($_POST, 'monto');
                if ($accion === 'depositar') {
                    $comision = $cuenta->depositar($monto);
                    $mensaje = 'Depósito de ' . dinero($monto) . ' realizado.';
                } else {
                    $comision = $cuenta->retirar($monto);
                    $mensaje = 'Retiro de ' . dinero($monto) . ' realizado.';
                }
                $mensaje .= ' Comisión: ' . dinero($comision) . '. Saldo: ' . dinero($cuenta->consultarSaldo()) . '.';
            } elseif ($accion === 'consultar') {
                $mensaje = 'El saldo de la cuenta ' . $seleccion . ' es ' . dinero($cuenta->consultarSaldo()) . '.';
            } elseif ($accion === 'intereses' && $cuenta instanceof CuentaAhorros) {
                $textoFecha = leerTexto($_POST, 'fecha');
                $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $textoFecha);
                if (!$fecha || $fecha->format('Y-m-d') !== $textoFecha) {
                    throw new InvalidArgumentException('Selecciona una fecha válida para el abono.');
                }
                $intereses = $cuenta->abonarIntereses($fecha);
                $mensaje = 'Intereses de ' . dinero($intereses) . ' abonados para el ' . $fecha->format('d/m/Y') . '.';
            } else {
                throw new InvalidArgumentException('La operación no está disponible para esta cuenta.');
            }
        }
        $_SESSION['aviso'] = ['tipo' => 'exito', 'texto' => $mensaje];
    } catch (InvalidArgumentException | DomainException $error) {
        $_SESSION['aviso'] = ['tipo' => 'error', 'texto' => $error->getMessage()];
    }

    header('Location: index.php' . ($seleccion !== null ? '?cuenta=' . $seleccion : ''), true, 303);
    exit;
}

// En el primer día real del mes, la visita procesa los abonos pendientes.
// No se cobran intereses repetidos aunque la página se vuelva a abrir.
$hoy = new DateTimeImmutable('today');
$abonosAutomaticos = [];
foreach ($cuentas as $cuenta) {
    if ($cuenta instanceof CuentaAhorros && $cuenta->puedeAbonarIntereses($hoy)) {
        try {
            $abono = $cuenta->abonarIntereses($hoy);
            $abonosAutomaticos[] = 'Cuenta ' . $cuenta->getNumeroCuenta() . ': ' . dinero($abono) . ' de intereses.';
        } catch (DomainException $error) {
            $abonosAutomaticos[] = 'Cuenta ' . $cuenta->getNumeroCuenta() . ': ' . $error->getMessage();
        }
    }
}

$aviso = $_SESSION['aviso'] ?? null;
$formulario = $_SESSION['formulario'] ?? [];
unset($_SESSION['aviso'], $_SESSION['formulario']);

function valorAnterior(string $campo, string $predeterminado = ''): string
{
    global $formulario;
    $valor = $formulario[$campo] ?? $predeterminado;
    return is_string($valor) ? escapar($valor) : escapar($predeterminado);
}

$cuentaSeleccionada = null;
if (isset($_GET['cuenta'])) {
    try {
        $numero = leerNumeroCuenta($_GET, 'cuenta');
        $cuentaSeleccionada = $cuentas[$numero] ?? null;
        if ($cuentaSeleccionada === null) {
            $aviso = ['tipo' => 'error', 'texto' => 'La cuenta solicitada no existe. Selecciona una de la lista.'];
        }
    } catch (InvalidArgumentException $error) {
        $aviso = ['tipo' => 'error', 'texto' => $error->getMessage()];
    }
} elseif ($cuentas) {
    $cuentaSeleccionada = reset($cuentas);
}
$tipoAnterior = valorAnterior('tipo', 'ahorros');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco HBC | Gestión de cuentas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header class="cabecera">
        <a class="marca" href="index.php" aria-label="Banco HBC, inicio">HBC<span>Banco</span></a>
        <span class="etiqueta">Ejercicio académico · PHP</span>
    </header>

    <main>
        <div class="introduccion">
            <h1>Tu cuenta, <span>en orden.</span></h1>
            <p>Crea cuentas de tus clientes y administra sus movimientos. Todos los valores están en pesos colombianos (COP).</p>
        </div>

        <?php if ($aviso): ?>
            <div class="aviso <?= escapar($aviso['tipo']) ?>" role="status"><?= escapar($aviso['texto']) ?></div>
        <?php endif; ?>
        <?php foreach ($abonosAutomaticos as $texto): ?>
            <div class="aviso" role="status"><?= escapar($texto) ?></div>
        <?php endforeach; ?>

        <div class="distribucion">
            <aside class="panel crear">
                <h2>Abrir una cuenta</h2>
                <p class="ayuda">Registra al cliente y elige su tipo de cuenta.</p>
                <form action="index.php" method="post">
                    <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
                    <input type="hidden" name="accion" value="crear">
                    <label for="numero">Número de cuenta</label>
                    <input id="numero" name="numero" type="text" inputmode="numeric" pattern="[1-9][0-9]{0,8}" maxlength="9" placeholder="Ej. 1001" value="<?= valorAnterior('numero') ?>" required aria-describedby="numero-ayuda">
                    <small id="numero-ayuda">De 1 a 9 cifras, sin ceros al inicio.</small>

                    <label for="nombre">Nombre del cliente</label>
                    <input id="nombre" name="nombre" type="text" maxlength="100" autocomplete="name" placeholder="Nombre y apellido" value="<?= valorAnterior('nombre') ?>" required>

                    <label for="tipo">Tipo de cuenta</label>
                    <select id="tipo" name="tipo">
                        <option value="ahorros" <?= $tipoAnterior === 'ahorros' ? 'selected' : '' ?>>Ahorros</option>
                        <option value="corriente" <?= $tipoAnterior === 'corriente' ? 'selected' : '' ?>>Corriente</option>
                    </select>

                    <label for="saldo">Saldo inicial (COP)</label>
                    <input id="saldo" name="saldo" type="number" min="0" max="1000000000" step="0.01" value="<?= valorAnterior('saldo', '0') ?>" required>

                    <div id="campo-interes">
                        <label for="interes">Interés mensual (%)</label>
                        <input id="interes" name="interes" type="number" min="0" max="100" step="0.01" value="<?= valorAnterior('interes', '1') ?>" required aria-describedby="interes-ayuda">
                        <small id="interes-ayuda">Solo para ahorros. Porcentaje elegido para el ejercicio.</small>
                    </div>
                    <button type="submit">Crear cuenta</button>
                </form>
                <p class="nota">Las cuentas se guardan en la sesión de este navegador. Usa datos ficticios.</p>
            </aside>

            <div class="contenido">
                <section class="lista-cuentas" aria-labelledby="titulo-cuentas">
                    <div class="titulo-fila">
                        <h2 id="titulo-cuentas">Cuentas de clientes</h2>
                        <span class="contador"><?= count($cuentas) ?> registradas</span>
                    </div>
                    <?php if (!$cuentas): ?>
                        <div class="vacio">
                            <h3>Empieza con tu primera cuenta</h3>
                            <p>Completa el formulario de apertura. Aquí podrás seleccionar cada cliente para consultar su saldo y realizar operaciones.</p>
                        </div>
                    <?php else: ?>
                        <nav class="cuentas" aria-label="Seleccionar cuenta">
                            <?php foreach ($cuentas as $cuenta): ?>
                                <?php $activa = $cuentaSeleccionada === $cuenta; ?>
                                <a class="cuenta <?= $activa ? 'activa' : '' ?>" href="?cuenta=<?= $cuenta->getNumeroCuenta() ?>" <?= $activa ? 'aria-current="true"' : '' ?>>
                                    <span><strong><?= escapar($cuenta->getNombreCliente()) ?></strong><small><?= escapar($cuenta->getTipo()) ?> · N.º <?= $cuenta->getNumeroCuenta() ?></small></span>
                                    <span class="cifra <?= $cuenta->consultarSaldo() < 0 ? 'negativo' : '' ?>"><?= dinero($cuenta->consultarSaldo()) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    <?php endif; ?>
                </section>

                <?php if ($cuentaSeleccionada): ?>
                    <?php $cuenta = $cuentaSeleccionada; ?>
                    <section class="detalle" aria-labelledby="titulo-detalle">
                        <div class="titulo-fila">
                            <h2 id="titulo-detalle"><?= escapar($cuenta->getNombreCliente()) ?></h2>
                            <span class="etiqueta"><?= escapar($cuenta->getTipo()) ?> · <?= $cuenta->getNumeroCuenta() ?></span>
                        </div>
                        <div class="resumen">
                            <div>
                                <p class="ayuda">Saldo actual</p>
                                <p class="saldo <?= $cuenta->consultarSaldo() < 0 ? 'negativo' : '' ?>"><?= dinero($cuenta->consultarSaldo()) ?></p>
                            </div>
                            <form action="index.php" method="post">
                                <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
                                <input type="hidden" name="cuenta" value="<?= $cuenta->getNumeroCuenta() ?>">
                                <button class="secundario" name="accion" value="consultar">Consultar saldo</button>
                            </form>
                        </div>

                        <?php if ($cuenta instanceof CuentaCorriente): ?>
                            <dl class="condiciones">
                                <div><dt>Comisión por depósito y retiro</dt><dd>4×1000 (0,4 %)</dd></div>
                                <div><dt>Límite de sobregiro</dt><dd><?= dinero(CuentaCorriente::LIMITE_SOBREGIRO) ?></dd></div>
                                <div><dt>Sobregiro utilizado</dt><dd><?= dinero($cuenta->getSobregiroUtilizado()) ?></dd></div>
                                <div><dt>Sobregiro disponible</dt><dd><?= dinero($cuenta->getSobregiroDisponible()) ?></dd></div>
                            </dl>
                            <p class="nota">El depósito acredita el monto menos el 4×1000. El retiro descuenta el monto más el 4×1000; ambos deben caber dentro del límite de sobregiro.</p>
                        <?php else: ?>
                            <dl class="condiciones">
                                <div><dt>Interés mensual</dt><dd><?= number_format($cuenta->getPorcentajeInteresMensual(), 2, ',', '.') ?> %</dd></div>
                                <div><dt>Último mes abonado</dt><dd><?= escapar($cuenta->getUltimoMesAbonado() ?? 'Sin abonos') ?></dd></div>
                            </dl>
                            <p class="nota">Los retiros no pueden superar el saldo. Los intereses se abonan el primer día del mes, una sola vez por período.</p>
                        <?php endif; ?>

                        <div class="operaciones">
                            <?php foreach (['depositar' => 'Depositar', 'retirar' => 'Retirar'] as $accion => $titulo): ?>
                                <form action="index.php" method="post">
                                    <h3><?= $titulo ?></h3>
                                    <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
                                    <input type="hidden" name="cuenta" value="<?= $cuenta->getNumeroCuenta() ?>">
                                    <input type="hidden" name="accion" value="<?= $accion ?>">
                                    <label for="monto-<?= $accion ?>">Monto (COP)</label>
                                    <input id="monto-<?= $accion ?>" name="monto" type="number" min="0.01" max="1000000000" step="0.01" placeholder="0,00" required>
                                    <button class="<?= $accion === 'retirar' ? 'secundario' : '' ?>" type="submit"><?= $titulo ?> dinero</button>
                                </form>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($cuenta instanceof CuentaAhorros): ?>
                            <div class="intereses">
                                <h3>Abonar intereses</h3>
                                <p class="ayuda" id="fecha-ayuda">Para probar la regla, elige el día 1 de un mes. La fecha simula el abono; debe ser posterior al último mes abonado.</p>
                                <form class="form-fecha" action="index.php" method="post">
                                    <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
                                    <input type="hidden" name="cuenta" value="<?= $cuenta->getNumeroCuenta() ?>">
                                    <input type="hidden" name="accion" value="intereses">
                                    <div><label for="fecha">Fecha del abono</label><input id="fecha" name="fecha" type="date" value="<?= $hoy->format('Y-m-d') ?>" required aria-describedby="fecha-ayuda"></div>
                                    <button class="secundario" type="submit">Abonar intereses</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="historial" aria-labelledby="titulo-movimientos">
                        <h2 id="titulo-movimientos">Movimientos</h2>
                        <div class="tabla-contenedor" role="region" aria-label="Historial de movimientos, desplázate para ver las columnas" tabindex="0">
                            <table>
                                <caption class="solo-lectores">Movimientos de la cuenta <?= $cuenta->getNumeroCuenta() ?>. Valores en COP.</caption>
                                <thead><tr><th scope="col">Fecha</th><th scope="col">Operación</th><th scope="col">Monto</th><th scope="col">Comisión</th><th scope="col">Saldo final</th></tr></thead>
                                <tbody>
                                    <?php foreach ($cuenta->getMovimientos() as $movimiento): ?>
                                        <tr>
                                            <td><?= escapar($movimiento['fecha']) ?></td>
                                            <td><?= escapar($movimiento['concepto']) ?></td>
                                            <td><?= dinero($movimiento['monto']) ?></td>
                                            <td><?= dinero($movimiento['comision']) ?></td>
                                            <td class="<?= $movimiento['saldo'] < 0 ? 'negativo' : '' ?>"><?= dinero($movimiento['saldo']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>Banco HBC · Práctica de herencia en PHP. Operaciones simuladas, sin dinero real.</footer>
    <script src="formulario.js"></script>
</body>
</html>
