<?php

// Pruebas de las reglas de la guía. Ejecutar: php herencia-cuenta/pruebas.php
// Este archivo se ejecuta únicamente en la terminal.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/clases/CuentaAhorros.php';
require_once __DIR__ . '/clases/CuentaCorriente.php';
date_default_timezone_set('America/Bogota');
$pruebas = 0;

function comprobar(bool $condicion, string $descripcion): void
{
    global $pruebas;
    if (!$condicion) {
        throw new RuntimeException('FALLÓ: ' . $descripcion);
    }
    $pruebas++;
    echo 'OK: ' . $descripcion . PHP_EOL;
}

function debeRechazar(callable $operacion, string $descripcion): void
{
    try {
        $operacion();
    } catch (InvalidArgumentException | DomainException $error) {
        comprobar(true, $descripcion);
        return;
    }
    comprobar(false, $descripcion);
}

$ahorros = new CuentaAhorros(1001, 'Ana Gómez', 100000, 1);
comprobar($ahorros instanceof Cuenta && $ahorros instanceof OperacionesCuenta, 'Herencia e interfaz');
comprobar($ahorros->getNumeroCuenta() === 1001 && $ahorros->getNombreCliente() === 'Ana Gómez', 'Datos del cliente');
comprobar($ahorros->consultarSaldo() === 100000.0, 'Saldo inicial');
comprobar($ahorros->depositar(50000) === 0.0 && $ahorros->consultarSaldo() === 150000.0, 'Depósito sin comisión en ahorros');
$ahorros->retirar(150000);
comprobar($ahorros->consultarSaldo() === 0.0, 'Ahorros permite retirar todo el saldo');
debeRechazar(function () use ($ahorros) { $ahorros->retirar(0.01); }, 'Ahorros no permite sobregiro');
comprobar($ahorros->consultarSaldo() === 0.0 && count($ahorros->getMovimientos()) === 3, 'Retiro rechazado no cambia saldo ni historial');

foreach ([0, -100, NAN, INF, 0.001, Cuenta::MONTO_MAXIMO + 1] as $monto) {
    debeRechazar(function () use ($ahorros, $monto) { $ahorros->depositar($monto); }, 'Rechaza depósito inválido');
    debeRechazar(function () use ($ahorros, $monto) { $ahorros->retirar($monto); }, 'Rechaza retiro inválido');
}
debeRechazar(function () { new CuentaAhorros(1, '', 0, 1); }, 'Rechaza nombre vacío');
debeRechazar(function () { new CuentaAhorros(0, 'Ana', 0, 1); }, 'Rechaza número de cuenta inválido');
debeRechazar(function () { new CuentaCorriente(1, 'Ana', -1); }, 'Rechaza saldo inicial negativo');
debeRechazar(function () { new CuentaAhorros(1, 'Ana', 0, 101); }, 'Rechaza interés mayor a 100 %');
debeRechazar(function () { new CuentaAhorros(1, 'Ana', 0, -1); }, 'Rechaza interés negativo');

$decimales = new CuentaAhorros(2, 'Luis', 0.30, 0);
$decimales->retirar(0.10);
$decimales->retirar(0.20);
comprobar($decimales->consultarSaldo() === 0.0, 'Redondeo a dos decimales en retiros');

$interes = new CuentaAhorros(3, 'Laura', 100000, 1);
debeRechazar(function () use ($interes) { $interes->abonarIntereses(new DateTimeImmutable('2026-10-02')); }, 'Intereses solo el primer día');
comprobar($interes->abonarIntereses(new DateTimeImmutable('2026-10-01')) === 1000.0, 'Calcula el interés mensual');
comprobar($interes->consultarSaldo() === 101000.0, 'Deposita los intereses en el saldo');
debeRechazar(function () use ($interes) { $interes->abonarIntereses(new DateTimeImmutable('2026-10-01')); }, 'No duplica el abono del mes');
debeRechazar(function () use ($interes) { $interes->abonarIntereses(new DateTimeImmutable('2026-09-01')); }, 'No abona meses anteriores al último');
comprobar($interes->abonarIntereses(new DateTimeImmutable('2026-11-01')) === 1010.0, 'Nuevo mes usa el saldo actualizado');
comprobar($interes->getUltimoMesAbonado() === '2026-11', 'Recuerda el último mes abonado');

$corriente = new CuentaCorriente(2001, 'Carlos Ruiz', 100000);
comprobar($corriente->depositar(50000) === 200.0 && $corriente->consultarSaldo() === 149800.0, 'Depósito corriente cobra 4x1000');
comprobar($corriente->retirar(200000) === 800.0 && $corriente->consultarSaldo() === -51000.0, 'Retiro corriente cobra 4x1000 y permite sobregiro');
comprobar($corriente->getSobregiroUtilizado() === 51000.0 && $corriente->getSobregiroDisponible() === 249000.0, 'Calcula el sobregiro utilizado y disponible');
$cantidadMovimientos = count($corriente->getMovimientos());
debeRechazar(function () use ($corriente) { $corriente->retirar(249000); }, 'Incluye la comisión al verificar el sobregiro');
comprobar($corriente->consultarSaldo() === -51000.0 && count($corriente->getMovimientos()) === $cantidadMovimientos, 'Sobregiro rechazado no altera la cuenta');
$corriente->depositar(100000);
comprobar($corriente->consultarSaldo() === 48600.0 && $corriente->getSobregiroUtilizado() === 0.0, 'Depósito paga la deuda y cobra su comisión');

$limite = new CuentaCorriente(2002, 'Sofía', 1200);
$limite->retirar(300000);
comprobar($limite->consultarSaldo() === -300000.0, 'Permite llegar exactamente al límite de sobregiro');
debeRechazar(function () use ($limite) { $limite->retirar(0.01); }, 'Rechaza un centavo sobre el límite');

$maximo = new CuentaAhorros(4, 'Pedro', Cuenta::MONTO_MAXIMO, 1);
debeRechazar(function () use ($maximo) { $maximo->depositar(0.01); }, 'Evita exceder el saldo máximo');
debeRechazar(function () use ($maximo) { $maximo->abonarIntereses(new DateTimeImmutable('2026-10-01')); }, 'Abono demasiado grande no modifica la cuenta');
comprobar($maximo->getUltimoMesAbonado() === null, 'Abono rechazado no marca el mes como pagado');

// PHP debe poder guardar y recuperar los objetos de la sesión.
$recuperada = unserialize(serialize($corriente));
comprobar($recuperada instanceof CuentaCorriente && $recuperada->consultarSaldo() === 48600.0, 'Conserva clase y saldo al guardar en sesión');

echo PHP_EOL . $pruebas . ' comprobaciones correctas.' . PHP_EOL;
