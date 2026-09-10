import { chromium } from "file:///C:/Users/Alejandro%20Soche/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright/index.mjs";

const base = "http://127.0.0.1:8765/Proyectos_Medios_Pago";
const browser = await chromium.launch({
  headless: true,
  executablePath: "C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe"
});
const errors = [];
const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
page.on("console", (message) => {
  if (message.type() === "error") errors.push(message.text());
});

await page.goto(`${base}/index.html`, { waitUntil: "networkidle" });
await page.getByRole("heading", { name: "Tus servicios, en una sola lectura." }).waitFor();
await page.screenshot({ path: ".impeccable/review/desktop.png", fullPage: true });
await page.getByLabel("Correo electrónico").fill("demo@correo.com");
if (await page.locator(".login-story").evaluate((el) => el.style.getPropertyValue("--login-progress")) !== "0.52") {
  throw new Error("Login meter did not respond to focus");
}
await page.getByLabel("Contraseña", { exact: true }).fill("demo1234");
await page.getByRole("button", { name: "Entrar al portal" }).click();
await page.waitForURL("**/pagar.html");
if (page.url().includes("?")) throw new Error("Login exposed values in URL");

await page.getByRole("button", { name: "Pagar" }).first().click();
await page.getByLabel("Número de tarjeta").fill("4111111111111111");
if (await page.getByLabel("Número de tarjeta").inputValue() !== "4111 1111 1111 1111") {
  throw new Error("Card formatting failed");
}
await page.getByLabel("Nombre del titular").fill("Persona Demo");
await page.getByLabel("Vencimiento").fill("1228");
await page.getByLabel("CVV").fill("123");
await page.getByRole("button", { name: /Simular pago de/ }).click();
await page.getByRole("heading", { name: "Simulación exitosa" }).waitFor();

await page.goto(`${base}/pagar.html`, { waitUntil: "networkidle" });
await page.getByRole("button", { name: "Pagar" }).nth(1).click();
await page.getByRole("tab", { name: /Débito bancario/ }).click();
if (await page.locator("#payment-workspace").evaluate((el) => el.style.getPropertyValue("--payment-progress")) !== ".72") {
  throw new Error("Payment meter did not respond to PSE selection");
}
await page.getByLabel("Tipo de persona").selectOption({ label: "Natural" });
await page.getByLabel("Documento").fill("123456789");
await page.getByLabel("Entidad financiera").selectOption({ label: "Bancolombia" });
await page.locator(".consent-row input").check();
await page.getByRole("button", { name: /Continuar a PSE/ }).click();
await page.getByRole("heading", { name: "Simulación exitosa" }).waitFor();
if (!(await page.locator(".success-message").innerText()).includes("No se abrió ningún banco")) {
  throw new Error("PSE simulation message missing");
}

await page.goto(`${base}/registro.html`, { waitUntil: "networkidle" });
await page.getByLabel("País").selectOption("CO");
if (!(await page.getByLabel("Ciudad").isEnabled())) throw new Error("City selector stayed disabled");
if (await page.getByLabel("Ciudad").locator("option").count() !== 6) throw new Error("City options failed");

const mobile = await browser.newPage({ viewport: { width: 390, height: 844 } });
mobile.on("console", (message) => {
  if (message.type() === "error") errors.push(message.text());
});
await mobile.goto(`${base}/pagar.html`, { waitUntil: "networkidle" });
if (await mobile.locator("body").evaluate((el) => el.scrollWidth > window.innerWidth)) {
  throw new Error("Mobile horizontal overflow");
}
await mobile.screenshot({ path: ".impeccable/review/mobile.png", fullPage: true });

if (errors.length) throw new Error("Console errors: " + errors.join(" | "));
console.log("PASS: login, tarjeta, PSE, registro, responsive y consola");
await browser.close();
