const formulario = document.getElementById("form-pedido");
const sabores = ["nap", "jq", "muz"];

// Solo los tamaños y cantidades de las pizzas marcadas son obligatorios.
function actualizarCampos() {
    sabores.forEach(function (sabor) {
        const marcada = document.getElementById("check-" + sabor).checked;
        const tamano = document.getElementById("tamano-" + sabor);
        const cantidad = document.getElementById("cant-" + sabor);

        tamano.disabled = !marcada;
        cantidad.disabled = !marcada;
        tamano.required = marcada;
        cantidad.required = marcada;
    });
}

formulario.addEventListener("change", actualizarCampos);
formulario.addEventListener("reset", function () {
    // Esperamos a que el navegador restablezca los checkboxes.
    setTimeout(actualizarCampos, 0);
});

formulario.addEventListener("submit", function (event) {
    const hayPizza = sabores.some(function (sabor) {
        return document.getElementById("check-" + sabor).checked;
    });

    if (!hayPizza) {
        event.preventDefault();
        alert("Debes seleccionar al menos una pizza para continuar.");
    }
});

// También se sincronizan al volver al formulario desde el recibo.
window.addEventListener("pageshow", actualizarCampos);
actualizarCampos();
