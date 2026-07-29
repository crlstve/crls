// Configuración
const STAR_COUNT  = 256;
const MAX_DEPTH   = 64;
const LINE_LENGTH = 0.05;  // longitud de la estela relativa a la velocidad
const SCROLL_GAIN = 0.08;  // velocidad que añade cada píxel de scroll
const MAX_SPEED   = 12;    // velocidad máxima del warp
const FRICTION    = 0.92;  // frenado por frame (más bajo = para antes)

const canvas = document.getElementById("tutorial");
const ctx = canvas.getContext("2d");

let stars = [];
let speed = 0;                                    // 0 = puntos estáticos
let lastScrollY = window.scrollY || window.pageYOffset;

function randomRange(min, max) {
  return Math.random() * (max - min) + min;
}

function resize() {
  canvas.width = innerWidth;
  canvas.height = innerHeight;
}

function initStars() {
  stars = [];
  for (let i = 0; i < STAR_COUNT; i++) {
    stars.push({
      x: randomRange(-25, 25),
      y: randomRange(-25, 25),
      z: randomRange(1, MAX_DEPTH)
    });
  }
}

// El tema lo marca la clase .dark del <html> (ver dark-mode.js)
function isDarkMode() {
  return document.documentElement.classList.contains("dark");
}

// El scroll inyecta velocidad; da igual la dirección (hacia arriba o abajo)
function onScroll() {
  const y = window.scrollY || window.pageYOffset;
  speed = Math.min(MAX_SPEED, speed + Math.abs(y - lastScrollY) * SCROLL_GAIN);
  lastScrollY = y;
}

function animate() {
  const halfWidth = canvas.width / 2;
  const halfHeight = canvas.height / 2;
  const dark = isDarkMode();

  if (dark) {
    ctx.fillStyle = "rgba(0,0,0,1)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  } else {
    // Lienzo transparente: deja ver el fondo blanco de la página
    ctx.clearRect(0, 0, canvas.width, canvas.height);
  }

  // La velocidad decae hacia 0 cuando dejas de hacer scroll
  speed *= FRICTION;
  if (speed < 0.01) speed = 0;

  ctx.lineCap = "round";
  ctx.lineJoin = "round";

  for (let i = 0; i < stars.length; i++) {
    const star = stars[i];

    // Solo se desplazan si hay velocidad; con speed === 0 quedan estáticos
    star.z -= speed / 10;

    if (star.z <= 0 || star.z >= MAX_DEPTH) {
      star.x = randomRange(-25, 25);
      star.y = randomRange(-25, 25);
      star.z = star.z <= 0 ? MAX_DEPTH : 0;
    }

    const k = 255 / star.z;
    const px = star.x * k + halfWidth;
    const py = star.y * k + halfHeight;

    if (px < 0 || px > canvas.width || py < 0 || py > canvas.height) continue;

    const size = 1 - (star.z / MAX_DEPTH) * 1.5;
    if (size <= 0) continue;                        // estrellas lejanas: invisibles

    // En oscuro las cercanas son blancas; en claro, grises oscuras
    const shade = (dark ? size * 255 : 255 - size * 255) | 0;
    const ox = size * (px - halfWidth) * speed * LINE_LENGTH;
    const oy = size * (py - halfHeight) * speed * LINE_LENGTH;

    ctx.lineWidth = size * 4;
    ctx.strokeStyle = "rgb(" + shade + "," + shade + "," + shade + ")";
    ctx.beginPath();
    ctx.moveTo(px, py);
    ctx.lineTo(px + ox, py + oy);                   // con speed 0 la estela es un punto
    ctx.stroke();
  }

  requestAnimationFrame(animate);
}

resize();
initStars();
addEventListener("resize", resize);
addEventListener("scroll", onScroll, { passive: true });
requestAnimationFrame(animate);
