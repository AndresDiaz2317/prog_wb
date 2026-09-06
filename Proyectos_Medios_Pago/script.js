const select_paises = {
    CO: {
        ciudad: ["Bogotá", "Medellín", "Cali", "Barranquilla", "Cartagena"],
        departamento: ["Bogotá D.C.", "Antioquia", "Valle del Cauca", "Atlántico", "Bolívar"]
    },

    MX: {
        ciudad: ["Ciudad de México", "Guadalajara", "Monterrey", "Puebla", "Tijuana"],
        departamento: ["Ciudad de México", "Jalisco", "Nuevo León", "Puebla", "Baja California"]
    },

    AR: {
        ciudad: ["Buenos Aires", "Córdoba", "Rosario", "Mendoza", "San Miguel de Tucumán"],
        departamento: ["Ciudad Autónoma de Buenos Aires", "Córdoba", "Santa Fe", "Mendoza", "Tucumán"]
    },

    CL: {
        ciudad: ["Santiago", "Valparaíso", "Concepción", "La Serena", "Antofagasta"],
        departamento: ["Región Metropolitana de Santiago", "Valparaíso", "Biobío", "Coquimbo", "Antofagasta"]
    },

    ES: {
        ciudad: ["Madrid", "Barcelona", "Valencia", "Sevilla", "Zaragoza"],
        departamento: ["Comunidad de Madrid", "Cataluña", "Comunidad Valenciana", "Andalucía", "Aragón"]
    },

    PE: {
        ciudad: ["Lima", "Arequipa", "Trujillo", "Chiclayo", "Cusco"],
        departamento: ["Lima", "Arequipa", "La Libertad", "Lambayeque", "Cusco"]
    }
};

const select_pais = document.getElementById("pais");
const select_ciudad = document.getElementById("ciudad");
const select_departamento = document.getElementById("departamento");

select_pais.addEventListener("change", function(){
    const select_v = select_pais.value;
    
    select_ciudad.innerHTML = `<option value=""> Elige una ciudad </option>`;
    select_departamento.innerHTML = '<option value=""> Elige un departamento </option>';
    
    if (select_paises[select_v]) {
        const info = select_paises[select_v];

        info.departamento.forEach(function(departamento) {
            select_departamento.innerHTML += `<option value="${departamento}">${departamento}</option>`;
        });

        info.ciudad.forEach(function(ciudad) {
            select_ciudad.innerHTML += `<option value="${ciudad}">${ciudad}</option>`;
        });
    }
});