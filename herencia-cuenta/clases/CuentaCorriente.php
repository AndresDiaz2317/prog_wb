<?php

require_once __DIR__ . '/Cuenta.php';

class CuentaCorriente extends Cuenta
{
    public const TASA_4X1000 = 4 / 1000;
    public const LIMITE_SOBREGIRO = 300000;

    public function getTipo(): string
    {
        return 'Corriente';
    }

    // Sobrescribimos estos métodos: las operaciones heredadas los usan.
    protected function calcularComision(float $monto): float
    {
        return round($monto * self::TASA_4X1000, 2);
    }

    protected function getSaldoMinimo(): float
    {
        return -self::LIMITE_SOBREGIRO;
    }

    public function getSobregiroUtilizado(): float
    {
        return max(0, -$this->saldo);
    }

    public function getSobregiroDisponible(): float
    {
        return round(self::LIMITE_SOBREGIRO - $this->getSobregiroUtilizado(), 2);
    }
}
