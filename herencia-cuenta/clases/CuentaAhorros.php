<?php

require_once __DIR__ . '/Cuenta.php';

class CuentaAhorros extends Cuenta
{
    private float $porcentajeInteresMensual;
    private ?string $ultimoMesAbonado = null;

    public function __construct(int $numeroCuenta, string $nombreCliente, float $saldoInicial, float $porcentaje)
    {
        if (!is_finite($porcentaje) || $porcentaje < 0 || $porcentaje > 100) {
            throw new InvalidArgumentException('El interés mensual debe estar entre 0 y 100 %.');
        }
        // Reutilizamos el constructor de la clase padre.
        parent::__construct($numeroCuenta, $nombreCliente, $saldoInicial);
        $this->porcentajeInteresMensual = $porcentaje;
    }

    public function getTipo(): string
    {
        return 'Ahorros';
    }

    public function getPorcentajeInteresMensual(): float
    {
        return $this->porcentajeInteresMensual;
    }

    public function getUltimoMesAbonado(): ?string
    {
        return $this->ultimoMesAbonado;
    }

    public function puedeAbonarIntereses(DateTimeImmutable $fecha): bool
    {
        $mes = $fecha->format('Y-m');
        return $fecha->format('d') === '01'
            && ($this->ultimoMesAbonado === null || $mes > $this->ultimoMesAbonado);
    }

    // La fecha puede pasarse para probar el primer día del mes en la guía.
    public function abonarIntereses(?DateTimeImmutable $fecha = null): float
    {
        $fecha = $fecha ?? new DateTimeImmutable('today');
        if ($fecha->format('d') !== '01') {
            throw new DomainException('Los intereses solo se abonan el primer día del mes.');
        }
        if (!$this->puedeAbonarIntereses($fecha)) {
            throw new DomainException('Este mes o un mes posterior ya recibió intereses. Elige un mes nuevo.');
        }

        $intereses = round($this->saldo * $this->porcentajeInteresMensual / 100, 2);
        $nuevoSaldo = round($this->saldo + $intereses, 2);
        if ($nuevoSaldo > self::MONTO_MAXIMO) {
            throw new DomainException('El abono supera el saldo máximo de esta demostración.');
        }
        $this->saldo = $nuevoSaldo;
        $this->ultimoMesAbonado = $fecha->format('Y-m');
        $this->registrarMovimiento('Intereses del ' . $fecha->format('d/m/Y'), $intereses, 0);
        return $intereses;
    }
}
