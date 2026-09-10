---
name: Portal de pagos
description: Un sistema operativo sereno que convierte cada pago simulado en una lectura clara, gradual y verificable.
colors:
  ink: "#132a3a"
  ink-secondary: "#274352"
  petrol: "#124e61"
  teal: "#176b87"
  teal-soft: "#d8e8e8"
  signal: "#f06c3b"
  signal-deep: "#cf4f24"
  paper: "#f4f1e8"
  paper-bright: "#fffdf7"
  paper-deep: "#e9e4d8"
  line: "#c9c6ba"
  muted: "#5d6c72"
  success: "#2f745b"
  error: "#a93a2c"
typography:
  display:
    fontFamily: "Manrope, Segoe UI, sans-serif"
    fontSize: "clamp(3.2rem, 6.2vw, 6rem)"
    fontWeight: 800
    lineHeight: 0.98
    letterSpacing: "-0.04em"
  headline:
    fontFamily: "Manrope, Segoe UI, sans-serif"
    fontSize: "clamp(2.5rem, 5vw, 5rem)"
    fontWeight: 800
    lineHeight: 1
    letterSpacing: "-0.04em"
  title:
    fontFamily: "Manrope, Segoe UI, sans-serif"
    fontSize: "clamp(1.8rem, 3vw, 2.5rem)"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-0.035em"
  body:
    fontFamily: "Manrope, Segoe UI, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.55
    letterSpacing: "normal"
  label:
    fontFamily: "Manrope, Segoe UI, sans-serif"
    fontSize: "0.72rem"
    fontWeight: 800
    lineHeight: 1
    letterSpacing: "0.12em"
rounded:
  xs: "5px"
  nav: "9px"
  badge: "10px"
  control: "12px"
  notice: "14px"
  surface: "16px"
  pill: "999px"
  circle: "50%"
spacing:
  xs: "8px"
  sm: "12px"
  md: "18px"
  lg: "24px"
  xl: "32px"
  section: "42px"
  surface: "52px"
  page: "72px"
components:
  button-primary:
    backgroundColor: "{colors.teal}"
    textColor: "{colors.paper-bright}"
    typography: "{typography.body}"
    rounded: "{rounded.control}"
    padding: "11px 18px"
    height: "46px"
  button-primary-hover:
    backgroundColor: "{colors.petrol}"
    textColor: "{colors.paper-bright}"
    rounded: "{rounded.control}"
    padding: "11px 18px"
    height: "46px"
  button-secondary:
    backgroundColor: "{colors.teal-soft}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.control}"
    padding: "11px 18px"
    height: "46px"
  input:
    backgroundColor: "{colors.paper-bright}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.control}"
    padding: "12px 14px"
    height: "50px"
  filter-chip-active:
    backgroundColor: "{colors.paper-bright}"
    textColor: "{colors.ink}"
    rounded: "{rounded.pill}"
    padding: "8px 13px"
  card-surface:
    backgroundColor: "{colors.paper-bright}"
    textColor: "{colors.ink}"
    rounded: "{rounded.surface}"
    padding: "clamp(26px, 4vw, 54px)"
---

# Design System: Portal de pagos

## Overview

**Creative North Star: "Lectura de medidor"**

El sistema convierte una tarea financiera simulada en una lectura progresiva y comprensible. Carátulas numeradas, líneas calibradas, agujas naranjas y cifras tabulares hacen visible el avance sin imitar la estética de un banco; el marfil mate y el azul petróleo sostienen una atmósfera serena, directa y académica.

La interfaz se siente instrumental pero humana: jerarquías grandes, superficies contenidas y estados inequívocos reducen la duda antes de pedir datos sensibles. La expresión vive en el movimiento funcional de los indicadores y en el contraste puntual de la señal, mientras el contenido conserva una densidad cómoda para operar en escritorio o móvil.

**Key Characteristics:**

- Paleta mate de marfil y azul petróleo con una señal naranja escasa y precisa.
- Números, reglas finas, carátulas y agujas como lenguaje recurrente de progreso.
- Tipografía Manrope compacta, firme y altamente legible.
- Superficies de formulario elevadas; catálogos y navegación mayormente planos.
- Estados de foco, avance, éxito y error visibles sin depender solo del texto.

## Colors

La paleta separa el plano de lectura cálido de la instrumentación fría y reserva el naranja para aquello que cambia, avanza o requiere atención.

### Primary

- **Azul petróleo operativo:** Base de paneles narrativos y resúmenes; concentra contexto, contraste y confianza sin parecer una interfaz bancaria genérica.
- **Turquesa de control:** Acciones primarias, marca, enlaces y estados completados; es el color interactivo persistente.

### Secondary

- **Naranja señal:** Agujas, lecturas activas, totales y foco visible; comunica posición o cambio, no decoración.
- **Naranja señal profundo:** Refuerza estados de mayor intensidad y marcas secundarias relacionadas con el flujo.

### Neutral

- **Tinta profunda:** Texto principal, controles silenciosos y mensajes flotantes.
- **Tinta secundaria:** Etiquetas y texto funcional de apoyo con más presencia que el contenido atenuado.
- **Marfil mate:** Fondo continuo de la aplicación.
- **Marfil luminoso:** Formularios, superficies activas y tarjetas en interacción.
- **Marfil profundo:** Controles deshabilitados y capas neutrales de menor énfasis.
- **Línea calibrada:** Divisores, reglas, contornos y escalas del medidor.
- **Lectura atenuada:** Texto secundario, metadatos y estados inactivos.
- **Verde confirmación:** Lectura final y éxito.
- **Rojo de error:** Mensajes y bordes de validación fallida.

### Named Rules

**The Signal Is a Needle Rule.** El naranja señala foco, avance, selección o total; nunca llena grandes superficies sin una función de estado.

**The Warm Canvas Rule.** La experiencia descansa sobre marfil, no sobre blanco clínico ni sobre grises azulados de banca genérica.

## Typography

**Display Font:** Manrope (con Segoe UI y sans-serif como respaldo)  
**Body Font:** Manrope (con Segoe UI y sans-serif como respaldo)

**Character:** Una sola familia geométrica mantiene continuidad entre cifras, formularios y titulares. Los pesos altos y el espaciado negativo de los encabezados evocan una carátula compacta; el cuerpo conserva amplitud y serenidad.

### Hierarchy

- **Display:** Peso extrafuerte, escala fluida y línea muy compacta; reservado para el relato de acceso y el resultado final.
- **Headline:** Peso extrafuerte, escala fluida y línea unitaria; abre catálogos y secciones principales con un ancho contenido.
- **Title:** Peso extrafuerte y tracking apretado; identifica formularios, pasos y superficies operativas.
- **Body:** Peso regular y línea amplia; explica acciones y límites de la simulación, normalmente dentro de medidas de 54 a 62 caracteres.
- **Label:** Peso extrafuerte, caja alta y tracking abierto; introduce lecturas, secciones y estados breves.

### Named Rules

**The One Dial Face Rule.** Manrope cubre toda la interfaz; la jerarquía se construye con escala, peso, tracking y cifras tabulares, no mezclando familias.

## Layout

El sistema utiliza contenedores centrados de hasta 1440px, márgenes laterales fluidos que alcanzan 72px y una retícula operativa de 12 columnas en formularios. Acceso divide relato y acción en una composición asimétrica; registro contrapone un riel de 320px con la superficie de captura; pagos separa formulario y resumen en una proporción aproximada de 1.45 a 0.55. El ritmo combina espacios compactos de 8–24px dentro de controles con separaciones de 32–72px entre bloques.

A 1080px, el catálogo pasa de cuatro a tres columnas y los paneles reducen su proporción. A 820px, las composiciones se apilan, el encabezado deja de ser fijo y el resumen del pago sube antes del formulario. A 600px, la retícula se vuelve de una columna, el catálogo adopta filas compactas, las acciones ocupan todo el ancho y los pasos ocultan detalle secundario sin perder la numeración.

**The Reading Order Rule.** Al apilar, el contexto y el estado preceden siempre al campo o acción que explican.

## Elevation & Depth

La profundidad es híbrida y contenida. Los fondos, reglas y cambios tonales organizan la mayor parte de la interfaz; solo las superficies de captura, el flujo de pago, los botones primarios y los mensajes flotantes se elevan. Los motivos circulares concéntricos aparecen dentro de paneles oscuros como geometría de medidor, nunca como adorno suelto.

### Shadow Vocabulary

- **Superficie operativa:** `0 18px 50px rgba(19, 42, 58, .12)`; eleva formularios y el flujo de pago sobre el lienzo marfil.
- **Acción primaria:** `0 9px 22px rgba(23, 107, 135, .24)`; acompaña el botón en reposo y crece en hover.
- **Acción primaria activa:** `0 12px 28px rgba(18, 78, 97, .3)`; confirma elevación al apuntar.
- **Mensaje flotante:** `0 12px 32px rgba(19, 42, 58, .28)`; separa el toast del contenido sin competir con él.

### Named Rules

**The Lift Only What Acts Rule.** Las sombras se reservan para superficies de tarea o respuesta inmediata; navegación, filtros y catálogo permanecen planos por defecto.

## Shapes

Los controles usan curvas contenidas de 12px, las superficies principales 16px y las notas intermedias 14px. Chips y avatares emplean cápsulas o círculos completos; pasos, agujas y medidores recurren a geometría circular calibrada. Los bordes de 1px y las reglas horizontales mantienen precisión visual y evitan que las curvas vuelvan blanda la interfaz.

**The Contained Curve Rule.** Redondear sirve para agrupar y proteger una lectura; no se aplican cápsulas a botones rectangulares ni curvas exageradas a superficies grandes.

## Components

### Buttons

- **Shape:** Rectángulos compactos con esquinas contenidas, altura mínima cómoda y peso fuerte.
- **Primary:** Turquesa con texto claro, elevación baja y composición centrada; puede ocupar todo el ancho al cerrar un formulario.
- **Hover / Focus:** Sube 2px, profundiza hacia azul petróleo y amplía su sombra; el foco usa un contorno naranja de 3px separado 3px.
- **Secondary / Quiet:** El secundario usa turquesa pálido; el silencioso conserva fondo transparente y solo revela una capa de tinta muy ligera al apuntar.

### Chips

- **Style:** Filtros en cápsula, texto pequeño y fuerte; inactivos transparentes, activos sobre marfil luminoso con línea calibrada.
- **State:** Hover aumenta contraste textual; selección añade superficie y borde sin cambiar dimensiones.

### Cards / Containers

- **Corner Style:** Los formularios y flujos usan curvas de superficie; las tarjetas del catálogo forman una retícula ortogonal sin radios individuales.
- **Background:** Marfil luminoso en superficies de captura y al pasar sobre una tarjeta; transparente en reposo dentro del catálogo.
- **Shadow Strategy:** Solo formularios y flujos completos usan la sombra de superficie operativa.
- **Border:** Reglas de 1px dividen tarjetas, secciones y pares etiqueta–valor.
- **Internal Padding:** 26px en tarjetas; las superficies grandes escalan fluidamente entre 26px y 54px.

### Inputs / Fields

- **Style:** Fondo marfil luminoso, línea calibrada, altura mínima de 50px y esquinas de control.
- **Focus:** El borde vira a turquesa y aparece un halo exterior turquesa translúcido; el foco global conserva además la señal naranja cuando corresponde.
- **Error / Disabled:** Error usa rojo contenido; deshabilitado baja a marfil profundo con texto atenuado y cursor no disponible.

### Navigation

La cabecera es una regla horizontal mate, con marca a la izquierda, navegación centrada y estado de usuario al final. Los enlaces usan esquinas discretas y revelan una película de tinta al hover o en estado activo. En móvil se ocultan metadatos y elementos secundarios, conservando marca y salida.

### Meter Progress

La firma del sistema combina escala, línea de base, cifra tabular y aguja naranja. La aguja responde al foco y al progreso del formulario con una curva de desaceleración expresiva; el medidor final rota hasta la lectura completa. Todos los movimientos respetan `prefers-reduced-motion`.

### Payment Method Switch

Dos pestañas planas comparten una regla inferior. La selección no crea una tarjeta nueva: refuerza el texto y dibuja una aguja naranja de 3px dentro del borde, manteniendo visible la continuidad del flujo.

### Notices and Feedback

Las advertencias de demostración usan un tono melocotón cálido y texto marrón; las explicaciones PSE usan turquesa pálido; los mensajes flotantes invierten a tinta profunda con texto claro. El éxito presenta una carátula semicircular, lectura verde y acciones explícitas para reiniciar o salir.

## Do's and Don'ts

### Do:

- **Do** usar el naranja para conectar foco, selección y avance con una lectura visible.
- **Do** mantener cifras de progreso, importes y referencias con numerales tabulares.
- **Do** conservar etiquetas explícitas, foco visible y mensajes claros sobre la naturaleza simulada del flujo.
- **Do** apilar contexto, resumen y formulario en un orden que preserve la lectura en móvil.
- **Do** sostener la interfaz con marfil mate, reglas finas y superficies de captura puntualmente elevadas.

### Don't:

- **Don't** convertir el portal en una cuadrícula de tarjetas bancarias azules intercambiables.
- **Don't** usar el naranja como fondo decorativo dominante ni como sustituto indiscriminado del turquesa interactivo.
- **Don't** añadir sombras a cada tarjeta, filtro o bloque de navegación.
- **Don't** esconder la condición de demostración ni presentar el flujo como una transacción real.
- **Don't** desconectar agujas, lecturas o indicadores de los estados reales de foco, selección y progreso.
