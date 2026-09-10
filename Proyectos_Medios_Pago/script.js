const locations = {
    CO: {
        cities: ["Bogotá", "Medellín", "Cali", "Barranquilla", "Cartagena"],
        regions: ["Bogotá D.C.", "Antioquia", "Valle del Cauca", "Atlántico", "Bolívar"]
    },
    MX: {
        cities: ["Ciudad de México", "Guadalajara", "Monterrey", "Puebla", "Tijuana"],
        regions: ["Ciudad de México", "Jalisco", "Nuevo León", "Puebla", "Baja California"]
    },
    AR: {
        cities: ["Buenos Aires", "Córdoba", "Rosario", "Mendoza", "San Miguel de Tucumán"],
        regions: ["Ciudad Autónoma de Buenos Aires", "Córdoba", "Santa Fe", "Mendoza", "Tucumán"]
    },
    CL: {
        cities: ["Santiago", "Valparaíso", "Concepción", "La Serena", "Antofagasta"],
        regions: ["Región Metropolitana", "Valparaíso", "Biobío", "Coquimbo", "Antofagasta"]
    },
    ES: {
        cities: ["Madrid", "Barcelona", "Valencia", "Sevilla", "Zaragoza"],
        regions: ["Comunidad de Madrid", "Cataluña", "Comunidad Valenciana", "Andalucía", "Aragón"]
    },
    PE: {
        cities: ["Lima", "Arequipa", "Trujillo", "Chiclayo", "Cusco"],
        regions: ["Lima", "Arequipa", "La Libertad", "Lambayeque", "Cusco"]
    }
};

const toast = document.querySelector(".toast");
let toastTimer;

function showToast(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add("is-visible");
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 3600);
}

document.querySelectorAll("[data-demo-message]").forEach((element) => {
    element.addEventListener("click", (event) => {
        event.preventDefault();
        showToast(element.dataset.demoMessage);
    });
});

document.querySelectorAll(".password-toggle").forEach((button) => {
    button.addEventListener("click", () => {
        const input = button.closest(".input-shell").querySelector("input");
        const showing = input.type === "text";
        input.type = showing ? "password" : "text";
        button.setAttribute("aria-label", showing ? "Mostrar contraseña" : "Ocultar contraseña");
    });
});

const authForm = document.querySelector(".auth-form");

if (authForm) {
    const loginStory = document.querySelector(".login-story");
    const authFields = [...authForm.querySelectorAll("input")];
    authForm.addEventListener("focusin", (event) => {
        const index = authFields.indexOf(event.target);
        if (index >= 0) loginStory?.style.setProperty("--login-progress", String(.52 + index * .16));
    });
    authForm.addEventListener("focusout", () => {
        if (!authForm.contains(document.activeElement)) {
            const completed = authFields.filter((field) => field.type === "checkbox" ? field.checked : field.value).length;
            loginStory?.style.setProperty("--login-progress", String(Math.min(.84, .42 + completed * .13)));
        }
    });
    authForm.addEventListener("submit", (event) => {
        event.preventDefault();
        if (!authForm.reportValidity()) return;
        window.location.assign("pagar.html");
    });
}

const countrySelect = document.getElementById("pais");
const citySelect = document.getElementById("ciudad");
const regionSelect = document.getElementById("departamento");

if (countrySelect && citySelect && regionSelect) {
    countrySelect.addEventListener("change", () => {
        const data = locations[countrySelect.value];
        citySelect.innerHTML = '<option value="">Selecciona una ciudad</option>';
        regionSelect.innerHTML = '<option value="">Selecciona una región</option>';
        citySelect.disabled = !data;
        regionSelect.disabled = !data;

        if (!data) return;
        data.regions.forEach((region) => regionSelect.add(new Option(region, region)));
        data.cities.forEach((city) => citySelect.add(new Option(city, city)));
    });
}

const genderSelect = document.getElementById("genero");
const otherGenderWrap = document.getElementById("otro-genero-wrap");

if (genderSelect && otherGenderWrap) {
    genderSelect.addEventListener("change", () => {
        const visible = genderSelect.value === "o";
        otherGenderWrap.hidden = !visible;
        otherGenderWrap.querySelector("input").required = visible;
    });
}

const progressForm = document.querySelector("[data-progress-form]");

if (progressForm) {
    const progressText = document.querySelector(".reading-badge b");
    const railItems = [...document.querySelectorAll(".progress-rail li")];
    const requiredFields = [...progressForm.querySelectorAll("[required]")];
    const password = document.getElementById("contrasena");
    const confirmation = document.getElementById("confirmar_contrasena");
    const passwordError = document.getElementById("password-error");

    const updateProgress = () => {
        const completed = requiredFields.filter((field) => {
            if (field.type === "checkbox") return field.checked;
            return field.value.trim() !== "";
        }).length;
        const percentage = Math.round((completed / requiredFields.length) * 100);
        progressText.textContent = percentage + "%";
        const activeIndex = percentage < 35 ? 0 : percentage < 75 ? 1 : 2;
        railItems.forEach((item, index) => item.classList.toggle("is-active", index === activeIndex));
    };

    progressForm.addEventListener("input", updateProgress);
    progressForm.addEventListener("change", updateProgress);
    progressForm.addEventListener("reset", () => window.setTimeout(updateProgress));

    confirmation.addEventListener("input", () => {
        const mismatch = confirmation.value && confirmation.value !== password.value;
        confirmation.setCustomValidity(mismatch ? "Las contraseñas no coinciden." : "");
        passwordError.textContent = mismatch ? "Las contraseñas no coinciden." : "";
    });

    progressForm.addEventListener("submit", (event) => {
        if (confirmation.value !== password.value) {
            event.preventDefault();
            confirmation.setCustomValidity("Las contraseñas no coinciden.");
            confirmation.reportValidity();
            passwordError.textContent = "Las contraseñas no coinciden.";
            return;
        }
        window.sessionStorage.setItem("registrationComplete", "true");
    });
}

if (window.sessionStorage.getItem("registrationComplete") && document.querySelector(".auth-form")) {
    showToast("Cuenta de demostración creada. Ya puedes iniciar sesión.");
    window.sessionStorage.removeItem("registrationComplete");
}

const searchInput = document.getElementById("service-search");
const filterButtons = [...document.querySelectorAll(".filter-chip")];
const serviceCards = [...document.querySelectorAll(".service-card")];
const emptyResults = document.querySelector(".empty-results");
let currentFilter = "all";

function filterServices() {
    const query = (searchInput?.value || "").trim().toLocaleLowerCase("es");
    let visible = 0;
    serviceCards.forEach((card) => {
        const matchesCategory = currentFilter === "all" || card.dataset.category === currentFilter;
        const matchesSearch = card.dataset.name.toLocaleLowerCase("es").includes(query);
        const show = matchesCategory && matchesSearch;
        card.classList.toggle("is-hidden", !show);
        if (show) visible += 1;
    });
    if (emptyResults) emptyResults.hidden = visible !== 0;
}

if (searchInput) searchInput.addEventListener("input", filterServices);

filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
        currentFilter = button.dataset.filter;
        filterButtons.forEach((item) => item.classList.toggle("is-active", item === button));
        filterServices();
    });
});

const paymentWorkspace = document.getElementById("payment-workspace");
const serviceBrowser = document.querySelector(".service-browser");
const dashboardIntro = document.querySelector(".dashboard-intro");
const payButtons = document.querySelectorAll(".pay-service");
const selectedServiceLabels = document.querySelectorAll(".chosen-service");
const summaryName = document.querySelector(".summary-provider h3");
const summaryKind = document.querySelector(".summary-provider small");
const summaryLogo = document.querySelector(".summary-logo img");
const paymentForms = document.querySelectorAll(".method-panel");
let selectedService = "Vanti";

payButtons.forEach((button) => {
    button.addEventListener("click", () => {
        const card = button.closest(".service-card");
        selectedService = button.dataset.service;
        selectedServiceLabels.forEach((label) => label.textContent = selectedService);
        if (summaryName) summaryName.textContent = selectedService;
        if (summaryKind) summaryKind.textContent = button.dataset.kind;
        if (summaryLogo) {
            const sourceImage = card.querySelector("img");
            summaryLogo.src = sourceImage.src;
            summaryLogo.alt = sourceImage.alt;
        }
        paymentWorkspace.hidden = false;
        paymentWorkspace.style.setProperty("--payment-progress", ".58");
        serviceBrowser.hidden = true;
        dashboardIntro.hidden = true;
        paymentWorkspace.scrollIntoView({ behavior: "smooth", block: "start" });
        document.getElementById("payment-title").focus?.();
    });
});

document.querySelector(".back-to-services")?.addEventListener("click", () => {
    paymentWorkspace.hidden = true;
    serviceBrowser.hidden = false;
    dashboardIntro.hidden = false;
    document.getElementById("servicios")?.scrollIntoView({ behavior: "smooth", block: "start" });
});

const methodTabs = [...document.querySelectorAll("[data-method]")];

methodTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
        const method = tab.dataset.method;
        methodTabs.forEach((item) => {
            const active = item === tab;
            item.classList.toggle("is-active", active);
            item.setAttribute("aria-selected", String(active));
        });
        paymentForms.forEach((panel) => panel.hidden = panel.id !== method + "-panel");
        paymentWorkspace?.style.setProperty("--payment-progress", method === "pse" ? ".72" : ".64");
        document.querySelector(".payment-steps li:nth-child(2) small").textContent =
            method === "pse" ? "Débito bancario PSE" : "Tarjeta";
    });
});

paymentForms.forEach((form) => {
    const fields = [...form.querySelectorAll("input, select")];
    form.addEventListener("focusin", (event) => {
        const index = fields.indexOf(event.target);
        if (index < 0) return;
        const ratio = .64 + ((index + 1) / Math.max(fields.length, 1)) * .24;
        paymentWorkspace?.style.setProperty("--payment-progress", String(Math.min(.9, ratio)));
    });
});

const cardNumber = document.getElementById("card-number");
const cardExpiry = document.getElementById("card-expiry");
const cardCvv = document.getElementById("card-cvv");

if (cardNumber) {
    cardNumber.addEventListener("input", () => {
        const digits = cardNumber.value.replace(/\D/g, "").slice(0, 16);
        cardNumber.value = digits.replace(/(.{4})/g, "$1 ").trim();
    });
}

if (cardExpiry) {
    cardExpiry.addEventListener("input", () => {
        const digits = cardExpiry.value.replace(/\D/g, "").slice(0, 4);
        cardExpiry.value = digits.length > 2 ? digits.slice(0, 2) + " / " + digits.slice(2) : digits;
    });
}

if (cardCvv) {
    cardCvv.addEventListener("input", () => {
        cardCvv.value = cardCvv.value.replace(/\D/g, "").slice(0, 3);
    });
}

paymentForms.forEach((form) => {
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;

        const method = form.id === "pse-panel" ? "PSE" : "Tarjeta";
        const button = form.querySelector(".submit-payment");
        const original = button.innerHTML;
        button.disabled = true;
        button.textContent = method === "PSE" ? "Simulando redirección…" : "Procesando simulación…";

        window.setTimeout(() => {
            showSuccess(method);
            button.disabled = false;
            button.innerHTML = original;
        }, 900);
    });
});

function showSuccess(method) {
    const reference = "SIM-" + new Date().getFullYear() + "-" + Math.floor(100000 + Math.random() * 900000);
    paymentWorkspace.innerHTML = `
        <div class="success-state">
            <div class="success-meter" aria-hidden="true"><span>100</span><i></i></div>
            <h2>Simulación exitosa</h2>
            <p class="section-label section-label--after">Lectura completada</p>
            <p class="success-message">${method === "PSE"
                ? "Simulamos la redirección a tu entidad financiera. No se abrió ningún banco ni se realizó un débito real."
                : "El formulario de tarjeta se completó correctamente. No se almacenaron datos ni se procesó un cobro real."}</p>
            <dl class="receipt-data">
                <div><dt>Servicio</dt><dd>${selectedService}</dd></div>
                <div><dt>Método</dt><dd>${method}</dd></div>
                <div><dt>Referencia</dt><dd>${reference}</dd></div>
            </dl>
            <div class="success-actions">
                <button class="btn btn--primary" type="button" id="new-payment">Simular otro pago</button>
                <a class="btn btn--secondary" href="index.html">Cerrar sesión</a>
            </div>
        </div>
    `;
    paymentWorkspace.scrollIntoView({ behavior: "smooth", block: "start" });
    document.getElementById("new-payment").addEventListener("click", () => window.location.reload());
}
