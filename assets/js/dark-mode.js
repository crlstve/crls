// Toggle dark mode
function toggleDarkMode() {
    const html = document.documentElement;
    html.classList.toggle('dark');
}

document.addEventListener('DOMContentLoaded', function () {
    const modeButton = document.getElementById('btn_mode');

    if (!modeButton) {
        return;
    }

    modeButton.addEventListener('click', toggleDarkMode);
});