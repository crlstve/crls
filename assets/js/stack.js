// Carrusel de logos del stack (ver views/partials/stack.php)
// Va en un fichero aparte y no inline: la CSP no permite 'unsafe-inline'
// en script-src (ver controllers/functions.php)
const slider = document.querySelector("#stack .splide");

if (slider && window.Splide) {
  new window.Splide(slider, {
    type: "loop",
    drag: "free",
    focus: "center",
    perPage: 4,
    gap: 40,
    padding: { left: 24, right: 24 },
    autoScroll: { speed: 2 },
    arrows: false,
    pagination: false,
    rewind: false,
    breakpoints: {
      640: { perPage: 3 },
      768: { perPage: 4 },
    },
  }).mount(window.splide.Extensions);
}
