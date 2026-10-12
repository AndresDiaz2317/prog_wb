<?php

// Una interfaz indica qué métodos debe tener la clase que la implementa.
interface Calculable
{
    public function calcularSubtotal();
    public function calcularIva();
    public function calcularTotal();
}

class Persona
{
    public $nombre;
    public $direccion;
    public $telefono;

    // El constructor guarda los datos al crear el objeto con "new".
    public function __construct($nombre, $direccion, $telefono)
    {
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
    }
}

// Cliente hereda las propiedades de Persona y agrega el correo.
class Cliente extends Persona
{
    public $email;

    public function __construct($nombre, $direccion, $telefono, $email)
    {
        parent::__construct($nombre, $direccion, $telefono);
        $this->email = $email;
    }
}

class Pizza
{
    // Precios ficticios en COP, iguales para los tres sabores.
    const PRECIOS = [
        'porcion' => 6000,
        'pequena' => 18000,
        'mediana' => 28000,
        'grande' => 38000,
        'extra-grande' => 48000,
    ];

    const TAMANOS = [
        'porcion' => 'Porción',
        'pequena' => 'Pequeña (30 cm)',
        'mediana' => 'Mediana (36 cm)',
        'grande' => 'Grande (42 cm)',
        'extra-grande' => 'Extra grande (46 cm)',
    ];

    public $nombre;
    public $tamano;
    public $cantidad;
    public $precio;

    public function __construct($nombre, $tamano, $cantidad)
    {
        $this->nombre = $nombre;
        $this->tamano = self::TAMANOS[$tamano];
        $this->cantidad = $cantidad;
        $this->precio = self::PRECIOS[$tamano];
    }

    public function calcularImporte()
    {
        return $this->precio * $this->cantidad;
    }
}

class Pedido implements Calculable
{
    const IVA = 0.19; // Porcentaje elegido para el ejercicio académico.
    const CUPON = 'PIZZA10';
    const DESCUENTO = 0.10;

    public $cliente;
    public $pizzas = [];
    private $cupon = '';

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function agregarPizza(Pizza $pizza)
    {
        $this->pizzas[] = $pizza;
    }

    public function calcularSubtotal()
    {
        $subtotal = 0;
        foreach ($this->pizzas as $pizza) {
            $subtotal += $pizza->calcularImporte();
        }
        return $subtotal;
    }

    public function calcularIva()
    {
        // El IVA se calcula después de descontar el cupón.
        return round(($this->calcularSubtotal() - $this->calcularDescuento()) * self::IVA);
    }

    public function calcularTotal()
    {
        return $this->calcularSubtotal() - $this->calcularDescuento() + $this->calcularIva();
    }

    public function aplicarCupon($codigo)
    {
        $codigo = strtoupper(trim($codigo));
        if ($codigo !== '' && $codigo !== self::CUPON) {
            throw new InvalidArgumentException('Cupón no válido. Use PIZZA10 o deje el campo vacío.');
        }
        $this->cupon = $codigo;
    }

    public function calcularDescuento()
    {
        if ($this->cupon === self::CUPON) {
            return round($this->calcularSubtotal() * self::DESCUENTO);
        }
        return 0;
    }

    public function contarUnidades()
    {
        $unidades = 0;
        foreach ($this->pizzas as $pizza) {
            $unidades += $pizza->cantidad;
        }
        return $unidades;
    }

    public function estimarMinutosPreparacion()
    {
        // Regla simulada: 20 minutos por la primera unidad y 5 por cada adicional.
        return 20 + 5 * max(0, $this->contarUnidades() - 1);
    }
}
