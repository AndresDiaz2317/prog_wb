<?php

// La interfaz define las operaciones que debe ofrecer cualquier cuenta.
interface OperacionesCuenta
{
    public function depositar(float $monto): float;
    public function retirar(float $monto): float;
    public function consultarSaldo(): float;
}
