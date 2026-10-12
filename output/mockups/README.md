# Mockups de las interfaces

Mockups de baja fidelidad creados a partir de las interfaces de pizza y Banco HBC. Usan únicamente blanco, negro y grises, tipografía sencilla, contornos rectangulares y botones esquemáticos. La distribución y los textos se simplificaron para mostrar la estructura de las interfaces sin reproducir su acabado visual.

## Archivos para abrir en draw.io

- **mockup_pizza.drawio**: formulario de pedido de pizza, de 1000 × 1030 px. Incluye nombre, dirección, teléfono, correo, recuadro de imagen con cruz, precios, Napolitana, Jamón y queso, Muzzarela, tamaño, cantidad y botones de confirmación y cancelación. Los datos de entrega se organizan en dos columnas.
- **mockup_banco.drawio**: Banco HBC. Contiene tres páginas, seleccionables en las pestañas inferiores del editor:
  1. Apertura de cuenta (1280 × 1100 px): formulario y estado sin cuentas registradas.
  2. Cuenta de ahorros (1280 × 1370 px): cliente seleccionado, saldo, depósitos, retiros, abono de intereses e historial.
  3. Cuenta corriente (1280 × 1370 px): cliente seleccionado, saldo, comisión, sobregiro, depósitos, retiros e historial.

En [draw.io](https://app.diagrams.net/), elige **Dispositivo** y luego **Abrir un diagrama existente**, o usa **Archivo → Abrir desde → Dispositivo** para seleccionar el archivo `.drawio`.

Todos los elementos son formas, líneas y textos nativos editables, organizados en grupos por sección. No se incluyen imágenes ni fotografías. Para editar un componente dentro de un grupo, selecciona el grupo y haz clic de nuevo en el componente; también puedes desagruparlo. El fondo está bloqueado para evitar moverlo accidentalmente.

## Vistas previas

- `pizza.png`
- `banco_apertura.png`
- `banco_ahorros.png`
- `banco_corriente.png`

Los cuatro PNG se exportaron desde el editor real de draw.io después de cargar los archivos XML. Se verificó además que los XML fueran válidos y que contuvieran formas y textos editables.

Los clientes Ana Gómez y Carlos Ruiz, números 1001 y 2001 y saldos de $100.000,00 son datos ficticios de muestra. Las fechas del historial también son ilustrativas. Estos mockups representan el diseño; sus controles no ejecutan operaciones.

Interfaces de referencia: `Segunda_Pag/form_pizza.html`, `Segunda_Pag/pizz.css`, `herencia-cuenta/index.php` y `herencia-cuenta/estilos.css`.
