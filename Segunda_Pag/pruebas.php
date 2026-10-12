<?php

// Ejecutar: php Segunda_Pag/pruebas.php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/clases.php';
$pruebas = 0;

function comprobar($condicion, $descripcion)
{
    global $pruebas;
    if (!$condicion) {
        throw new RuntimeException('FALLÓ: ' . $descripcion);
    }
    $pruebas++;
    echo 'OK: ' . $descripcion . PHP_EOL;
}

$pedido = new Pedido(new Cliente('Ana', 'Calle 1', '3001234567', 'ana@example.com'));
$pedido->agregarPizza(new Pizza('Napolitana', 'pequena', 2));
$pedido->agregarPizza(new Pizza('Jamón y queso', 'mediana', 1));
comprobar($pedido->calcularSubtotal() === 64000, 'Subtotal sin modificar los precios');
comprobar($pedido->calcularDescuento() === 0 && $pedido->calcularTotal() == 76160, 'Sin cupón mantiene el total anterior');
comprobar($pedido->contarUnidades() === 3 && $pedido->estimarMinutosPreparacion() === 30, 'Suma cantidades de distintos sabores para estimar el tiempo');

$pedido->aplicarCupon(' pizza10 ');
comprobar($pedido->calcularDescuento() == 6400, 'Cupón acepta minúsculas y espacios y descuenta el 10 %');
comprobar($pedido->calcularIva() == 10944 && $pedido->calcularTotal() == 68544, 'IVA sobre el subtotal con descuento');
try {
    $pedido->aplicarCupon('OTRO');
    comprobar(false, 'Debe rechazar un cupón desconocido');
} catch (InvalidArgumentException $error) {
    comprobar($pedido->calcularDescuento() == 6400, 'Cupón inválido no modifica el pedido');
}
$pedido->aplicarCupon('');
comprobar($pedido->calcularDescuento() === 0 && $pedido->calcularTotal() == 76160, 'Campo vacío no aplica descuento');

$individual = new Pedido($pedido->cliente);
$individual->agregarPizza(new Pizza('Muzzarela', 'porcion', 1));
comprobar($individual->estimarMinutosPreparacion() === 20, 'Una unidad requiere 20 minutos simulados');
$grande = new Pedido($pedido->cliente);
$grande->agregarPizza(new Pizza('Muzzarela', 'grande', 100));
comprobar($grande->estimarMinutosPreparacion() === 515, 'La estimación incluye toda la cantidad solicitada');
echo PHP_EOL . $pruebas . ' comprobaciones correctas.' . PHP_EOL;
