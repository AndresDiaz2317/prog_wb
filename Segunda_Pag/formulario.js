// Variables

const formulario = document.getElementById("form-pedido");
const nap = document.getElementById("check-nap");
const jq = document.getElementById("check-jq");
const muz = document.getElementById("check-muz");
const cant_nap = document.getElementById("cant-nap");
const cant_jq = document.getElementById("cant-jq");
const cant_muz = document.getElementById("cant-muz");


// Avisos al usuario
formulario.addEventListener("submit", function(event){
    // Verifica que al menos una checkbox este marcada
    if (!nap.checked && !jq.checked && !muz.checked){
    event.preventDefault();
    alert("Debes seleccionar al menos una opcion para continuar")
    return;
    }
    
    // Verifica que si hay una checkbox marcada, esta tenga una cantidad < 1
    if (nap.checked && Number(cant_nap.value) < 1){
        event.preventDefault();
        alert("Ingrese una cantidad correcta");
        return;
    }

    if (jq.checked && Number(cant_jq.value) < 1 ){
        event.preventDefault();
        alert("Ingrese una cantidad correcta");
        return;
    }

    if (muz.checked && Number(cant_muz.value) < 1 ){
        event.preventDefault();
        alert("Ingrese una cantidad correcta");
        return;
    }




});

