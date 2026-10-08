// Esto solo adapta el formulario. Las reglas se validan también en PHP.
const tipoCuenta = document.getElementById('tipo');
const campoInteres = document.getElementById('campo-interes');
const interes = document.getElementById('interes');

function actualizarInteres() {
    const esAhorros = tipoCuenta.value === 'ahorros';
    campoInteres.hidden = !esAhorros;
    interes.disabled = !esAhorros;
    interes.required = esAhorros;
}

tipoCuenta.addEventListener('change', actualizarInteres);
actualizarInteres();
