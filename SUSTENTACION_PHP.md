# Guía breve para sustentar las mejoras en PHP

Hay dos mejoras por aplicación. Se usan clases, métodos, condicionales,
operaciones matemáticas, ciclos, formularios y sesiones; no requieren base de datos.

## Ejecutar ambos ejemplos

Desde la raíz del proyecto:

```sh
php -S localhost:8000 -t .
```

- Pizza: http://localhost:8000/Segunda_Pag/form_pizza.html
- Banco: http://localhost:8000/herencia-cuenta/index.php

En XAMPP también puedes abrir las mismas rutas debajo de la carpeta del proyecto
en `htdocs`. PHP requiere el servidor; Live Server no procesa estos archivos.

## Pizza: cupón de descuento

**Qué puedes decir:** “El formulario envía el código por POST. PHP comprueba si
es PIZZA10 y calcula el 10 % del subtotal. El navegador no decide el descuento;
lo calcula el método de la clase Pedido en el servidor.”

**Código:** `Segunda_Pag/clases.php`, métodos `aplicarCupon()` y
`calcularDescuento()`. `recibo.php` lee `$_POST` y muestra el resultado.

```php
if ($this->cupon === self::CUPON) {
    return round($this->calcularSubtotal() * self::DESCUENTO);
}
return 0;
```

**Demostración:** pide 2 napolitanas pequeñas y 1 de jamón y queso mediana.
Sin cupón: subtotal $64.000, IVA $12.160 y total $76.160.
Con `PIZZA10`: descuento $6.400, base $57.600, IVA $10.944 y total $68.544.
Con `OTRO`: PHP rechaza el código. También acepta `pizza10` en minúsculas.

**Conceptos para explicar:** `$_POST`, `if`, constantes, métodos, `trim()`,
`strtoupper()` y `round()`. El IVA del 19 % es el valor del ejercicio.

## Pizza: estimación de preparación

**Qué puedes decir:** “PHP recorre las pizzas con foreach y suma las cantidades,
no solo los sabores. Después aplica una fórmula: 20 minutos para la primera
unidad y 5 por cada adicional.”

**Código:** `Segunda_Pag/clases.php`, métodos `contarUnidades()` y
`estimarMinutosPreparacion()`.

```php
$unidades = 0;
foreach ($this->pizzas as $pizza) {
    $unidades += $pizza->cantidad;
}
```

**Demostración:** 1 unidad → 20 minutos; el pedido de 3 unidades → 30 minutos.
La regla cuenta porciones como unidades. Es una estimación simulada de
preparación, sin domicilio ni temporizador en vivo.

**Conceptos para explicar:** arreglos de objetos, `foreach`, acumulador, suma y
multiplicación. La regla se puede cambiar en un único método.

## Banco: meta de ahorro

**Qué puedes decir:** “Guardo una meta dentro del objeto Cuenta. Como ahorros y
corriente heredan de Cuenta, ambas tienen esta mejora. PHP compara el saldo
actual con la meta y envía al HTML el porcentaje para una barra progress.”

**Código:** `herencia-cuenta/clases/Cuenta.php`, propiedad `$metaAhorro`, métodos
`establecerMetaAhorro()` y `calcularProgresoMeta()`. `index.php` recibe el
formulario y la sesión conserva el objeto al recargar.

```php
$saldoPositivo = max(0, $this->saldo);
$porcentaje = round(min(100, $saldoPositivo / $this->metaAhorro * 100), 2);
```

Esta fórmula se usa solo si existe una meta, para evitar dividir entre cero.

**Demostración:** crea ahorros con saldo `100000`, interés `0` y meta `200000`.
Muestra 50 % y faltan $100.000. Deposita `100000`: muestra 100 % y meta alcanzada.
Retira `50000`: vuelve a 75 %. Recarga: el objetivo sigue guardado en la sesión.
Crear o cambiar la meta no modifica el saldo ni registra movimientos.

**Conceptos para explicar:** propiedad protegida, herencia, validación,
`$_SESSION`, división, `min()`, `max()` y HTML generado por PHP. Un sobregiro
representa deuda, así que se muestra 0 % de avance.

## Banco: extracto descargable

**Qué puedes decir:** “Al pulsar el enlace, PHP recupera el historial de la
cuenta de esta sesión. Con header indica al navegador que es una descarga y
con foreach y fputcsv escribe las filas en un archivo CSV.”

**Código:** `herencia-cuenta/extracto.php`. El enlace del historial envía el
número de cuenta por `$_GET`.

```php
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="extracto-' . $numero . '.csv"');
$archivo = fopen('php://output', 'w');
```

**Demostración:** después de hacer un depósito y un retiro, pulsa “Descargar
extracto CSV”. Abre el archivo en Excel o en un editor: contiene apertura,
depósito y retiro, con sus montos, comisiones y saldos. Si Excel no separa las
columnas automáticamente, importa el CSV eligiendo punto y coma como delimitador.

**Conceptos para explicar:** `$_GET`, sesión, `header()`, `foreach`,
`fopen()`, `fputcsv()` y `fclose()`. La descarga consulta datos y no mueve dinero.

## Preguntas que podrían hacerte

- **¿Por qué PHP?** Ejecuta la lógica en el servidor: valida el cupón, calcula
  valores, conserva los objetos en la sesión y genera el archivo descargable.
- **¿Qué hace JavaScript?** Ayuda a activar campos del formulario. Las cuatro
  mejoras funcionan aunque esté desactivado; sus cálculos están en PHP.
- **¿Dónde se guardan los datos?** El banco guarda objetos y metas en la sesión
  del navegador en el servidor. No usa base de datos. Pizza genera el recibo y
  no guarda pedidos.
- **¿Qué parte usa herencia?** La meta está en Cuenta y la reciben CuentaAhorros
  y CuentaCorriente mediante `extends`.
- **¿Son operaciones reales?** Son simulaciones académicas. Los tiempos y las
  reglas económicas son los valores elegidos para este ejercicio.

## Comprobar la lógica

```sh
php Segunda_Pag/pruebas.php
php herencia-cuenta/pruebas.php
```
