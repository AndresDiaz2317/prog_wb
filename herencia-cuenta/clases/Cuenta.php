<?php

require_once __DIR__ . '/OperacionesCuenta.php';

// Es abstracta porque solo crearemos cuentas de ahorros o corrientes.
abstract class Cuenta implements OperacionesCuenta
{
    protected int $numeroCuenta;
    protected string $nombreCliente;
    protected float $saldo;
    protected array $movimientos = [];

    // Límite técnico de esta demostración, para trabajar con dos decimales.
    public const MONTO_MAXIMO = 1000000000;

    public function __construct(int $numeroCuenta, string $nombreCliente, float $saldoInicial)
    {
        if ($numeroCuenta <= 0) {
            throw new InvalidArgumentException('El número de cuenta debe ser un entero positivo.');
        }
        $nombreCliente = trim($nombreCliente);
        if (!preg_match('/^.{1,100}$/usD', $nombreCliente)) {
            throw new InvalidArgumentException('Escribe un nombre de cliente de hasta 100 caracteres.');
        }
        if (!is_finite($saldoInicial) || $saldoInicial < 0 || $saldoInicial > self::MONTO_MAXIMO) {
            throw new InvalidArgumentException('El saldo inicial debe estar entre $0 y $1.000.000.000.');
        }

        $this->numeroCuenta = $numeroCuenta;
        $this->nombreCliente = $nombreCliente;
        $this->saldo = round($saldoInicial, 2);
        $this->registrarMovimiento('Apertura', $this->saldo, 0);
    }

    public function getNumeroCuenta(): int
    {
        return $this->numeroCuenta;
    }

    public function getNombreCliente(): string
    {
        return $this->nombreCliente;
    }

    public function consultarSaldo(): float
    {
        return $this->saldo;
    }

    public function getMovimientos(): array
    {
        // Primero se muestra el movimiento más reciente.
        return array_reverse($this->movimientos);
    }

    abstract public function getTipo(): string;

    public function depositar(float $monto): float
    {
        $monto = $this->validarMonto($monto);
        $comision = $this->calcularComision($monto);
        $nuevoSaldo = round($this->saldo + $monto - $comision, 2);
        if ($nuevoSaldo > self::MONTO_MAXIMO) {
            throw new DomainException('El depósito supera el saldo máximo de esta demostración.');
        }

        $this->saldo = $nuevoSaldo;
        $this->registrarMovimiento('Depósito', $monto, $comision);
        return $comision;
    }

    public function retirar(float $monto): float
    {
        $monto = $this->validarMonto($monto);
        $comision = $this->calcularComision($monto);
        $nuevoSaldo = round($this->saldo - $monto - $comision, 2);

        // Ahorros permite llegar a cero; corriente cambia este límite.
        if ($nuevoSaldo < $this->getSaldoMinimo()) {
            throw new DomainException('Saldo insuficiente para el retiro y su comisión. Revisa el monto.');
        }

        // El saldo solo cambia después de aprobar el retiro.
        $this->saldo = $nuevoSaldo;
        $this->registrarMovimiento('Retiro', $monto, $comision);
        return $comision;
    }

    protected function validarMonto(float $monto): float
    {
        if (!is_finite($monto) || $monto <= 0 || $monto > self::MONTO_MAXIMO) {
            throw new InvalidArgumentException('El monto debe ser positivo y no superar $1.000.000.000.');
        }
        $monto = round($monto, 2);
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto mínimo es $0,01.');
        }
        return $monto;
    }

    protected function calcularComision(float $monto): float
    {
        return 0;
    }

    protected function getSaldoMinimo(): float
    {
        return 0;
    }

    protected function registrarMovimiento(string $concepto, float $monto, float $comision): void
    {
        $this->movimientos[] = [
            'fecha' => date('d/m/Y H:i:s'),
            'concepto' => $concepto,
            'monto' => $monto,
            'comision' => $comision,
            'saldo' => $this->saldo,
        ];
    }
}
