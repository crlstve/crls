// Callback global para reCAPTCHA
window.recaptchaCallback = function() {
    document.getElementById('recaptcha-error').classList.add('hidden');
    document.dispatchEvent(new Event('recaptcha:changed'));
};

window.recaptchaExpiredCallback = function() {
    document.getElementById('recaptcha-error').textContent = 'La verificación reCAPTCHA ha expirado. Por favor, verifica nuevamente.';
    document.getElementById('recaptcha-error').classList.remove('hidden');
    document.dispatchEvent(new Event('recaptcha:changed'));
};

//Con el Dom cargado
document.addEventListener('DOMContentLoaded', function () {
//smooth scroll
    window.addEventListener('scroll', function () {
        var elements = document.querySelectorAll('.fadeInUp');
        var windowHeight = window.innerHeight;
        elements.forEach(function (element) {
            var position = element.getBoundingClientRect().top;
            if (position < windowHeight * 0.75) {
                element.classList.add('visible');
            }
        });
    });
//sound
    const hoverElements = document.querySelectorAll('.sound');
    const hoverAudio = document.getElementById('hoverSound');
    const actionAudio = document.getElementById('actionSound');
    hoverElements.forEach(element => {
        element.addEventListener('mouseenter', function () {
            hoverAudio.currentTime = 0; // Reinicia el audio al principio
            hoverAudio.play();
        });
        element.addEventListener('click', function () {
            actionAudio.currentTime = 0; // Reinicia el audio de acción al principio
            actionAudio.play();
        });
    });
//modal
    //open modal button
        const modal = document.getElementById('modal');
        const contactButton = document.getElementById('contact');
        contactButton.addEventListener('click', function() {
            modal.classList.toggle('hidden');
        });
    //close modal button
        modal.addEventListener('click', (e) => { if (e.target === modal) { modal.classList.add('hidden'); } });
    //form
    const contactForm = document.querySelector('#modal form');
    const inputs = contactForm.querySelectorAll('input[required], textarea[required]');
    const submitButton = contactForm.querySelector('input[type="submit"]');
    const errorMessage = document.getElementById('error');
    const recaptchaError = document.getElementById('recaptcha-error');
    const successMessage = document.getElementById('success');

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
    }
    //Validación de campos
    inputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (!this.value.trim()) {
                this.classList.add('error');
                this.classList.remove('success');
            } else {
                this.classList.add('success');
                this.classList.remove('error');
            }
        });
    });
    // Función para verificar todos los campos
    function checkAllFields() {
        return Array.from(inputs).every(input => input.value.trim() !== '');
    }
    
    // Función para verificar reCAPTCHA
    function checkRecaptcha() {
        return typeof grecaptcha !== 'undefined' && grecaptcha.getResponse().length > 0;
    }
    
    // Función para actualizar el estado del botón de envío
    function updateSubmitButton() {
        if (checkAllFields() && checkRecaptcha()) {
            submitButton.classList.remove('btnError');
            submitButton.classList.add('btnSuccess');
            submitButton.disabled = false;
        } else {
            submitButton.classList.remove('btnSuccess');
            submitButton.classList.add('btnError');
            submitButton.disabled = true;
        }
    }
    // Agregar evento input a cada campo
    inputs.forEach(input => {
        input.addEventListener('input', updateSubmitButton);
    });
    document.addEventListener('recaptcha:changed', updateSubmitButton);
    // Evento submit del formulario
    contactForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        // Ocultar mensajes de error previos
        errorMessage.classList.add('hidden');
        recaptchaError.classList.add('hidden');
        
        if (!checkAllFields()) {
            showError('Por favor, completa todos los campos.');
            return;
        }
        
        if (!checkRecaptcha()) {
            recaptchaError.classList.remove('hidden');
            updateSubmitButton();
            return;
        }

        submitButton.disabled = true;
        submitButton.classList.remove('btnSuccess');
        submitButton.classList.add('btnError');

        // Recoger los datos del formulario y crear un objeto
        const data = {
            nombre: contactForm.querySelector('[name="name"]').value.trim(),
            email: contactForm.querySelector('[name="email"]').value.trim(),
            phone: contactForm.querySelector('[name="phone"]').value.trim(),
            subject: contactForm.querySelector('[name="subject"]').value.trim(),
            message: contactForm.querySelector('[name="message"]').value.trim(),
            company: contactForm.querySelector('[name="company"]').value.trim(),
            'g-recaptcha-response': grecaptcha.getResponse()
        };

        try {
            const response = await fetch('./controllers/contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const payload = await response.json();

            if (!response.ok || payload.status !== 'success') {
                showError(payload.message || 'No se pudo enviar el formulario.');
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
                updateSubmitButton();
                return;
            }

            contactForm.reset();
            contactForm.classList.add('hidden');
            successMessage.classList.remove('hidden');
            if (typeof grecaptcha !== 'undefined') {
                grecaptcha.reset();
            }
        } catch (error) {
            console.error('Error sending contact form:', error);
            showError('No se pudo enviar el formulario. Inténtalo de nuevo en unos minutos.');
            if (typeof grecaptcha !== 'undefined') {
                grecaptcha.reset();
            }
        } finally {
            updateSubmitButton();
        };
    });
    
    // Actualizar el estado inicial del botón
    updateSubmitButton();
    //mensaje
    console.log(
        '¡Hola, gracias por tu interés!.\n' +
        'Llevo en el mundo del %cdesarrollo web%c más de %c8 años%c.\n' +
        'Mi especialidad es crear sitios web y aplicaciones web con %cWordpress%c.\n' +
        'Pero tengo experiencia con otros %clenguajes, frameworks y tecnologías%c.\n' +
        'Si quieres contactarme puedes hacerlo en  el formulario de %ccontacto%c.\n' +
        '¡Muchas gracias!'
        
        ,
        'color: #6ee7b7;', '', 'color: #6ee7b7;', '', 'color: #6ee7b7;', '',
        'color: #6ee7b7;', '', 'color: #6ee7b7;', ''
    );


    // Menu
    const menu = document.querySelector('[data-contoller="menu"]');
    const navMenu = document.getElementById('nav-menu');
    const navMenuBtn = Array.from(document.getElementsByClassName('btn_nav'));  // Convertir a array

    menu.addEventListener('click', function (e) {
        e.preventDefault();
        navMenu.classList.toggle('hidden');
    });

    // Ahora puedes usar forEach en el array de botones
    navMenuBtn.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            //e.preventDefault();
            navMenu.classList.toggle('hidden');
        });
    });



});