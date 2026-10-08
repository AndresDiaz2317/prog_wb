---
name: "Banco HBC · Gestión de cuentas"
colors:
  fondo: "#11100e"
  panel: "#1c1a17"
  superficie: "#24211c"
  dorado: "#dfbc72"
  dorado-claro: "#f0d498"
  texto: "#f5efe2"
  suave: "#bfb7a7"
  linea: "#4a4233"
  borde-campo: "#6c604a"
  error: "#ffb6a7"
  exito: "#b7d6aa"
typography:
  display:
    fontFamily: "Georgia, 'Times New Roman', serif"
    fontSize: "clamp(2.3rem, 4vw, 3.5rem)"
    fontWeight: 400
    lineHeight: 1.1
  body:
    fontFamily: "'Segoe UI', sans-serif"
rounded:
  controles: "6px"
  panel: "12px"
---

## Overview
Interfaz académica para abrir cuentas, consultar saldos y registrar movimientos en COP. El negro cálido y el dorado organizan la información con una apariencia sobria y fácil de leer.

## Colors
El dorado destaca la marca, los enlaces y las acciones principales; su variante clara resalta saldos, foco y botones al pasar el puntero. Los tonos oscuros separan fondo, panel de apertura y cuenta seleccionada. El texto suave identifica ayudas y datos secundarios, y los divisores separan secciones.
El error señala avisos y saldos negativos; el éxito confirma operaciones mediante un mensaje escrito.

## Typography
Georgia se usa en la marca, los encabezados y el saldo principal. Segoe UI aparece en textos explicativos, formularios, cuentas, condiciones e historial, sin descargar fuentes externas.
Los títulos de sección miden 1,55 rem y los subtítulos 1,2 rem. Las cifras usan números de ancho uniforme para facilitar la comparación.

## Layout
El contenido tiene un ancho máximo de 1320 px. En escritorio, el formulario de apertura ocupa una columna de 330 px y el área de cuentas y detalle ocupa el espacio restante; ambas se separan 40 px.
Hasta 1000 px, la columna lateral baja a 280 px y el espacio entre columnas a 28 px. Hasta 760 px, la apertura y el contenido se apilan; la fecha y su botón también se apilan. Hasta 420 px, condiciones y operaciones pasan a una columna.
El historial conserva sus columnas y permite desplazamiento horizontal dentro de su propio contenedor.

## Elevation & Depth
La profundidad se consigue con fondos de distinto tono y bordes finos. No hay sombras ni efectos decorativos.

## Shapes
Los controles y avisos tienen esquinas suaves; el panel de apertura usa una curva más amplia. La lista de cuentas y la tabla conservan filas rectas separadas por líneas.

## Components
Los botones principales usan fondo dorado y texto oscuro. Los secundarios tienen fondo transparente y borde dorado; al pasar el puntero adoptan el fondo dorado. Campos y botones tienen una altura mínima de 46 px.
La cuenta activa se distingue por superficie cálida y contorno dorado. Sin cuentas, un mensaje explica cómo empezar. Los avisos de resultado aparecen antes del formulario y del listado.
El foco de teclado usa un contorno dorado claro de 3 px, separado 4 px del control. Los botones deshabilitados reducen su opacidad al 55 %.

## Do's and Don'ts
- Mantener negro, dorado y tonos cálidos; reservar error y éxito para estados.
- Conservar etiquetas visibles, ayudas breves y valores monetarios alineados.
- Mantener el historial desplazable en pantallas pequeñas y evitar sombras o fuentes externas.
