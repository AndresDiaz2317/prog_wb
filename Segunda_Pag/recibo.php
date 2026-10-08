<?php
require_once __DIR__ . '/clases.php';

// El recibo se genera únicamente cuando se envía el formulario.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_pizza.html');
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');

// Rechazamos valores que no sean texto (por ejemplo, un arreglo enviado a mano).
function leerCampo($nombre)
{
    $valor = $_POST[$nombre] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

// Escapamos los datos al mostrarlos para que se vean como texto, nunca como HTML.
function escapar($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function dinero($valor)
{
    return '$' . number_format($valor, 0, ',', '.') . ' COP';
}

$nombre = leerCampo('usuario');
$direccion = leerCampo('direccion');
$telefono = leerCampo('telefono');
$email = leerCampo('email');
$errores = [];

if ($nombre === '') {
    $errores[] = 'Ingrese su nombre.';
}
if ($direccion === '') {
    $errores[] = 'Ingrese su dirección.';
}
if (!preg_match('/^[0-9+()\s-]{7,20}$/', $telefono)) {
    $errores[] = 'Ingrese un teléfono de 7 a 20 caracteres con al menos 7 dígitos.';
} elseif (strlen(preg_replace('/\D/', '', $telefono)) < 7) {
    $errores[] = 'El teléfono debe contener al menos 7 dígitos.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Ingrese un correo electrónico válido.';
}

// Creamos objetos usando las clases del archivo clases.php.
$cliente = new Cliente($nombre, $direccion, $telefono, $email);
$pedido = new Pedido($cliente);
$sabores = [
    'nap' => ['valor' => 'napolitana', 'nombre' => 'Napolitana'],
    'jq' => ['valor' => 'jamon-queso', 'nombre' => 'Jamón y queso'],
    'muz' => ['valor' => 'muzzarela', 'nombre' => 'Muzzarela'],
];
$seleccionadas = 0;

foreach ($sabores as $codigo => $sabor) {
    // Un checkbox sin marcar no llega en POST: ignoramos esa pizza.
    if (!isset($_POST['tipo-pizza-' . $codigo])) {
        continue;
    }
    $seleccionadas++;

    if (leerCampo('tipo-pizza-' . $codigo) !== $sabor['valor']) {
        $errores[] = 'Seleccione un sabor válido.';
        continue;
    }

    $tamano = leerCampo('tamano-pizza-' . $codigo);
    $cantidad = filter_var(leerCampo('cantidad-pizza-' . $codigo), FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 100],
    ]);

    if (!array_key_exists($tamano, Pizza::PRECIOS)) {
        $errores[] = 'Seleccione un tamaño para ' . $sabor['nombre'] . '.';
    }
    if ($cantidad === false) {
        $errores[] = 'Ingrese una cantidad entera entre 1 y 100 para ' . $sabor['nombre'] . '.';
    }
    if (array_key_exists($tamano, Pizza::PRECIOS) && $cantidad !== false) {
        $pedido->agregarPizza(new Pizza($sabor['nombre'], $tamano, $cantidad));
    }
}

if ($seleccionadas === 0) {
    $errores[] = 'Seleccione al menos una pizza.';
}
if ($errores) {
    http_response_code(422);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="recibo.css">
    <title>Recibo del pedido</title>
</head>
<body>
    <h1 class="titulos"><?= $errores ? 'Revise su pedido' : 'Recibo del pedido' ?></h1>
    <div class="recibo-div">
        <main class="recibo">
            <?php if ($errores): ?>
                <div role="alert">
                    <p>No se pudo confirmar el pedido:</p>
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= escapar($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <p>Use el botón Atrás del navegador para corregir los datos o comience un nuevo pedido.</p>
            <?php else: ?>
                <p>Nombre: <?= escapar($cliente->nombre) ?></p>
                <p>Dirección: <?= escapar($cliente->direccion) ?></p>
                <p>Teléfono: <?= escapar($cliente->telefono) ?></p>
                <p>Email: <?= escapar($cliente->email) ?></p>

                <h2>Detalle del pedido</h2>
                <div class="tabla-pedido">
                    <table>
                        <caption>Precios de ejemplo en pesos colombianos</caption>
                        <thead>
                            <tr>
                                <th scope="col">Pizza</th>
                                <th scope="col">Tamaño</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Precio unitario</th>
                                <th scope="col">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedido->pizzas as $pizza): ?>
                                <tr>
                                    <td><?= escapar($pizza->nombre) ?></td>
                                    <td><?= escapar($pizza->tamano) ?></td>
                                    <td><?= $pizza->cantidad ?></td>
                                    <td><?= dinero($pizza->precio) ?></td>
                                    <td><?= dinero($pizza->calcularImporte()) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <hr>
                <p>Subtotal: <?= dinero($pedido->calcularSubtotal()) ?></p>
                <p>IVA (19 %): <?= dinero($pedido->calcularIva()) ?></p>
                <p class="total">Total a pagar: <?= dinero($pedido->calcularTotal()) ?></p>
            <?php endif; ?>
        </main>
    </div>
    <div class="recibo-div">
        <a href="form_pizza.html" class="boton-nv">Nuevo pedido</a>
    </div>
</body>
</html>
