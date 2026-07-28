// Rastro de píxeles que sigue al cursor sobre las .card
const CELL      = 14;    // lado del píxel en px
const RADIUS    = 3.2;   // alcance del pincel, en celdas
const DECAY     = 0.9;   // desvanecido por frame (más bajo = estela más corta)
const MAX_ALPHA = 0.38;  // opacidad de la celda más encendida
const MIN_LEVEL = 0.02;  // por debajo de esto la celda se apaga

const FALLBACK_FROM = [255, 10, 96];   // red
const FALLBACK_TO   = [202, 255, 61];  // primary

// Las variables --pixel-* del CSS vienen como "255 10 96"
function parseRgb(value, fallback) {
  const parts = value.trim().split(/[\s,]+/).map(Number);
  const valid = parts.length === 3 && parts.every((n) => !Number.isNaN(n));
  return valid ? parts : fallback;
}

function rgbToHsl(rgb) {
  const r = rgb[0] / 255;
  const g = rgb[1] / 255;
  const b = rgb[2] / 255;
  const max = Math.max(r, g, b);
  const min = Math.min(r, g, b);
  const delta = max - min;
  const l = (max + min) / 2;

  if (!delta) return [0, 0, l * 100];

  let h;
  if (max === r) h = ((g - b) / delta) % 6;
  else if (max === g) h = (b - r) / delta + 2;
  else h = (r - g) / delta + 4;
  h *= 60;
  if (h < 0) h += 360;

  return [h, (delta / (1 - Math.abs(2 * l - 1))) * 100, l * 100];
}

// Mezclar en HSL por el camino corto de la rueda: red y primary se cruzan
// pasando por naranja y amarillo, sin los tonos apagados que da mezclar en RGB
function mixHsl(from, to, t) {
  let arc = to[0] - from[0];
  if (arc > 180) arc -= 360;
  if (arc < -180) arc += 360;

  const h = (from[0] + arc * t + 360) % 360;
  const s = from[1] + (to[1] - from[1]) * t;
  const l = from[2] + (to[2] - from[2]) * t;

  return "hsl(" + h.toFixed(1) + " " + s.toFixed(1) + "% " + l.toFixed(1) + "%)";
}

function createPixelTrail(card) {
  const canvas = document.createElement("canvas");
  canvas.className = "card-pixels";
  canvas.setAttribute("aria-hidden", "true");
  card.prepend(canvas);

  const ctx = canvas.getContext("2d");

  let cols = 0;
  let rows = 0;
  let level  = new Float32Array(0);   // cuánto está encendida cada celda (0..1)
  let jitter = new Float32Array(0);   // dithering fijo por celda: borde irregular sin parpadeo
  let colors = [];                    // color ya resuelto de cada celda
  let hovering = false;
  let running  = false;

  // El color de cada celda sale de su posición: degradado diagonal sobre la card
  function paintColors() {
    const styles = getComputedStyle(card);
    const from = rgbToHsl(parseRgb(styles.getPropertyValue("--pixel-from"), FALLBACK_FROM));
    const to   = rgbToHsl(parseRgb(styles.getPropertyValue("--pixel-to"), FALLBACK_TO));
    const span = Math.max(1, cols + rows - 2);

    colors = new Array(cols * rows);
    for (let row = 0; row < rows; row++) {
      for (let col = 0; col < cols; col++) {
        colors[row * cols + col] = mixHsl(from, to, (col + row) / span);
      }
    }
  }

  function resize() {
    const rect = card.getBoundingClientRect();
    if (!rect.width || !rect.height) return;

    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    cols = Math.ceil(rect.width / CELL);
    rows = Math.ceil(rect.height / CELL);

    canvas.width  = Math.round(rect.width * dpr);
    canvas.height = Math.round(rect.height * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    level  = new Float32Array(cols * rows);
    jitter = new Float32Array(cols * rows);
    for (let i = 0; i < jitter.length; i++) {
      jitter[i] = 0.55 + Math.random() * 0.45;
    }
    paintColors();
  }

  // Enciende las celdas bajo el cursor; nunca las apaga (de eso se encarga el decay)
  function brush(clientX, clientY) {
    const rect = card.getBoundingClientRect();
    const cx = (clientX - rect.left) / CELL;
    const cy = (clientY - rect.top) / CELL;

    const minCol = Math.max(0, Math.floor(cx - RADIUS));
    const maxCol = Math.min(cols - 1, Math.ceil(cx + RADIUS));
    const minRow = Math.max(0, Math.floor(cy - RADIUS));
    const maxRow = Math.min(rows - 1, Math.ceil(cy + RADIUS));

    for (let row = minRow; row <= maxRow; row++) {
      for (let col = minCol; col <= maxCol; col++) {
        const dx = col + 0.5 - cx;
        const dy = row + 0.5 - cy;
        const distance = Math.sqrt(dx * dx + dy * dy);
        if (distance > RADIUS) continue;

        const i = row * cols + col;
        const value = (1 - distance / RADIUS) * jitter[i];
        if (value > level[i]) level[i] = value;
      }
    }
  }

  function frame() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    let alive = false;
    for (let row = 0; row < rows; row++) {
      for (let col = 0; col < cols; col++) {
        const i = row * cols + col;
        if (level[i] < MIN_LEVEL) {
          level[i] = 0;
          continue;
        }

        level[i] *= DECAY;
        alive = true;

        ctx.globalAlpha = level[i] * MAX_ALPHA;
        ctx.fillStyle = colors[i];
        ctx.fillRect(col * CELL, row * CELL, CELL - 1, CELL - 1);  // el hueco de 1px marca el píxel
      }
    }
    ctx.globalAlpha = 1;

    // Sigue vivo mientras quede estela por apagar o el cursor siga encima
    if (alive || hovering) requestAnimationFrame(frame);
    else running = false;
  }

  function start() {
    if (running) return;
    running = true;
    requestAnimationFrame(frame);
  }

  card.addEventListener("pointerenter", () => { hovering = true; });
  card.addEventListener("pointerleave", () => { hovering = false; });
  card.addEventListener("pointermove", (event) => {
    if (event.pointerType === "touch") return;
    brush(event.clientX, event.clientY);
    start();
  });

  new ResizeObserver(resize).observe(card);

  return paintColors;   // para refrescar los colores al cambiar de tema
}

const canHover = matchMedia("(hover: hover)").matches;
const wantsMotion = !matchMedia("(prefers-reduced-motion: reduce)").matches;

if (canHover && wantsMotion) {
  const cards = Array.from(document.querySelectorAll(".card")).map(createPixelTrail);

  // El toggle de tema cambia la clase .dark del <html> (ver dark-mode.js)
  new MutationObserver(() => cards.forEach((refresh) => refresh()))
    .observe(document.documentElement, { attributes: true, attributeFilter: ["class"] });
}
