# Herencia de cuentas en PHP · Banco HBC

Aplicación de la guía **Ejercicio herencia PHP**. Permite crear varias cuentas,
seleccionar clientes, depositar, retirar, consultar saldo y abonar intereses.
Interfaz en negro, dorado y tonos cálidos. Usa PHP, HTML, CSS y un JavaScript
pequeño, sin frameworks ni dependencias externas.

## Ejecutar

Necesitas **PHP 7.4 o superior**. Desde la raíz `prog_wb`:

```sh
php -S localhost:8000 -t herencia-cuenta
```

Abre **http://localhost:8000**. En XAMPP, copia `herencia-cuenta` a `htdocs`,
inicia Apache y abre **http://localhost/herencia-cuenta/**.
PHP necesita un servidor; Live Server o abrir el archivo con doble clic no lo ejecutan.

## Archivos y conceptos de la guía

| Archivo | Responsabilidad |
| --- | --- |
| `clases/OperacionesCuenta.php` | **Interfaz** que exige depositar, retirar y consultar saldo. |
| `clases/Cuenta.php` | **Clase padre abstracta** con propiedades protegidas, constructor, operaciones comunes e historial. |
| `clases/CuentaAhorros.php` | **Herencia** mediante `extends Cuenta`. Agrega porcentaje mensual y método de abono. Usa `parent::__construct()`. |
| `clases/CuentaCorriente.php` | **Herencia** con comisión y límite de sobregiro. Sobrescribe métodos usados por las operaciones de la clase padre. |
| `index.php` | Valida formularios, crea **objetos** con `new`, llama sus **métodos** y muestra resultados. |
| `estilos.css` | Presentación adaptable a escritorio y celular. |
| `formulario.js` | Muestra el interés solo al crear ahorros. PHP también valida los datos. |
| `pruebas.php` | Pruebas de las reglas de negocio, ejecutables en la terminal. |

Todas las cuentas tienen número de cuenta (`int`), nombre del cliente (`string`)
y saldo (`float`, con dos decimales). Las propiedades se protegen para que el
saldo cambie mediante los métodos y sus validaciones. La creación se hace con
el constructor del tipo elegido; `Cuenta` no se puede instanciar directamente.

## Reglas implementadas

- El número de cuenta debe ser único en la sesión, positivo y de 1 a 9 cifras,
  sin ceros al inicio. El cliente puede tener más de una cuenta.
- El saldo inicial es mayor o igual a cero. Depósitos y retiros deben ser
  positivos. Los formularios aceptan hasta dos decimales, sin separadores de miles.
- **Ahorros:** depósitos y retiros sin comisión. El retiro debe ser menor o
  igual al saldo. Intereses = saldo actual × porcentaje mensual / 100.
- **Intereses:** únicamente el día 1 de cada mes. Se recuerda el último mes
  abonado para evitar duplicados y retrocesos de período. Al visitar la página
  el primer día real del mes se abonan automáticamente los intereses pendientes
  de las cuentas de ahorros de la sesión. El formulario permite simular una
  fecha para comprobar esta regla; una simulación futura también actualiza el
  último mes y bloquea abonos de meses anteriores.
- **Corriente:** la frase de la guía «en cada transacción» se interpreta como
  comisión del **4×1000 en depósitos y retiros**. Depositar acredita monto menos
  comisión; retirar descuenta monto más comisión. Apertura y consulta no cobran.
- **Sobregiro:** saldo mínimo de **−$300.000**, incluyendo la comisión del retiro.
  Si ya hay deuda, un depósito la reduce primero. Se muestra cuánto sobregiro
  está utilizado y cuánto queda disponible.
- Cada operación aprobada registra monto, comisión y saldo final. Si falla,
  no cambia el saldo ni se agrega un movimiento.
- Montos, comisiones, saldos e intereses se redondean a dos decimales. Como
  límite técnico de esta demostración, montos y saldo positivo no superan
  $1.000.000.000; el porcentaje mensual está entre 0 y 100 %.

## Ejemplos para comprobar

1. Crea ahorros **1001**, cliente **Ana Gómez**, saldo **100000** e interés **1**.
   Deposita **50000**: saldo **$150.000,00**. Retira **200000**: se rechaza.
   Abona intereses con el día 1 de un mes nuevo: **$1.500,00** y saldo
   **$151.500,00**. Repite el mismo mes: se rechaza.
2. Crea corriente **2001**, cliente **Carlos Ruiz**, saldo **100000**.
   Deposita **50000**: comisión **$200,00**, saldo **$149.800,00**.
   Retira **200000**: comisión **$800,00**, saldo **−$51.000,00**.
   Retira **249000**: se rechaza, porque con la comisión excedería el límite.
3. Crea corriente **2002** con saldo **1200**. Retira **300000**:
   comisión **$1.200,00**, saldo **−$300.000,00**. Un centavo más se rechaza.

Si ejecutas los ejemplos el primer día del mes, toma en cuenta el abono
automático de ahorros antes de comparar el saldo.

## Pruebas

Desde la raíz del repositorio:

```sh
php herencia-cuenta/pruebas.php
```

Comprueban herencia e interfaz, depósitos, retiros, decimales, valores inválidos,
intereses, duplicados de mes, comisiones, límite de sobregiro y conservación de
los objetos al serializarlos para la sesión. Una falla termina con código de
salida distinto de cero.

## Alcance académico

Los objetos se guardan en `$_SESSION`: se conservan al navegar y recargar,
pero no hay base de datos y pueden perderse al vencer la sesión. No se ejecutan
tareas cuando el servidor está apagado o nadie visita la aplicación; no hay
abonos retroactivos. Para un banco real se necesitarían persistencia, un proceso
programado y otras medidas fuera del alcance de la guía.

Las operaciones se realizan por POST con token de formulario y redirección
para que recargar no repita el movimiento. Los datos se validan en PHP y los
nombres se escapan al mostrar HTML. Todo el dinero es simulado.
