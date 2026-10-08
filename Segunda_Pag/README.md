# Pedido de pizzas con PHP

## Cómo ejecutarlo

Necesitas PHP 7.4 o superior. Desde la raíz del repositorio ejecuta:

```sh
php -S localhost:8000 -t Segunda_Pag
```

Abre **http://localhost:8000/form_pizza.html**. También puedes copiar
`Segunda_Pag` en `htdocs` de XAMPP, iniciar Apache y abrir
`http://localhost/Segunda_Pag/form_pizza.html`.

PHP necesita un servidor: abrir el HTML con doble clic o usar únicamente
Live Server no ejecuta `recibo.php`.

## Cómo funciona

1. `form_pizza.html` solicita nombre, dirección, teléfono, email, sabores,
   tamaños y cantidades. Envía los datos por **POST** a `recibo.php`.
2. `formulario.js` activa los campos de las pizzas marcadas y comprueba que
   haya una selección. El formulario también funciona sin JavaScript.
3. `recibo.php` valida nuevamente todos los datos en el servidor, crea los
   objetos y muestra el detalle. Si hay errores, pide corregirlos; no genera
   un recibo parcial. Las cantidades deben ser enteras entre 1 y 100.
4. `clases.php` contiene la lógica orientada a objetos:
   - **Interfaz:** `Calculable` declara los métodos para subtotal, IVA y total.
   - **Herencia:** `Cliente extends Persona` reutiliza nombre, dirección y
     teléfono; añade el correo. `parent::__construct(...)` llama al constructor
     de la clase padre.
   - **Clases y propiedades:** `Pizza` guarda sabor, tamaño, cantidad y precio;
     `Pedido` guarda un cliente y una lista de pizzas.
   - **Constructores y objetos:** `new Cliente(...)`, `new Pedido(...)` y
     `new Pizza(...)` crean objetos con sus datos iniciales.
   - **Métodos:** `agregarPizza()` añade una pizza; `calcularImporte()` multiplica
     precio por cantidad. `Pedido implements Calculable` suma los importes,
     calcula el IVA y obtiene el total.

Los precios son ficticios, iguales para todos los sabores y se encuentran en
`Pizza::PRECIOS`. El IVA del 19 % es un valor elegido para esta actividad y
se puede cambiar en `Pedido::IVA`. No se guarda el pedido ni se envían correos.

Ejemplo: 2 napolitanas pequeñas ($36.000) y 1 de jamón y queso mediana
($28.000) dan subtotal **$64.000**, IVA **$12.160** y total **$76.160 COP**.
