<?php

// Cargamos las clases antes de recuperar los objetos de la sesión.
require_once __DIR__ . '/clases/CuentaAhorros.php';
require_once __DIR__ . '/clases/CuentaCorriente.php';
session_start();
header('Cache-Control: no-store');

$numero = $_GET['cuenta'] ?? '';
if (!is_string($numero) || !preg_match('/^[1-9][0-9]{0,8}$/D', $numero)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Selecciona un número de cuenta válido para descargar el extracto.');
}
$cuenta = $_SESSION['cuentas'][(int) $numero] ?? null;
if (!$cuenta instanceof Cuenta) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('La cuenta no existe en esta sesión. Vuelve al banco y selecciona una cuenta.');
}

// Estas cabeceras indican al navegador que descargue el resultado como archivo.
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="extracto-' . $numero . '.csv"');
$archivo = fopen('php://output', 'w');
fwrite($archivo, "\xEF\xBB\xBF"); // BOM: Excel reconoce las tildes en UTF-8.
fputcsv($archivo, ['Cuenta', 'Fecha', 'Operación', 'Monto (COP)', 'Comisión (COP)', 'Saldo final (COP)'], ';', '"', '');

// El CSV usa punto y coma entre columnas y coma para los decimales.
foreach ($cuenta->getMovimientos() as $movimiento) {
    fputcsv($archivo, [
        $numero,
        $movimiento['fecha'],
        $movimiento['concepto'],
        number_format($movimiento['monto'], 2, ',', ''),
        number_format($movimiento['comision'], 2, ',', ''),
        number_format($movimiento['saldo'], 2, ',', ''),
    ], ';', '"', '');
}
fclose($archivo);
exit;
