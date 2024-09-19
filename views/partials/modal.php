<div id="modal" class="hidden w-full h-full fixed top-0 left-0 bg-black/50 flex justify-center items-center z-50">
    <div class="block card w-fit p-16">
        <h2 class="text-3xl md:text-4xl font-bold font-title text-violet-500 dark:text-emerald-300 w-fit mx-auto mb-6">Contacto</h2>
        <form method="post" class="flex flex-col gap-3">
            <input type="text" name="name" id="name" placeholder="nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑçÇ\s]+" required>
            <input type="email" name="email" id="email" placeholder="email" required>
            <input type="tel" name="phone" id="phone" placeholder="teléfono" pattern="[0-9]+" required>
            <input type="text" name="subject" id="subject" placeholder="asunto" required>
            <textarea name="message" id="message" placeholder="mensaje" required></textarea>
            <input type="submit" name="submit" value="submit" class="btnError font-bold">
        </form>
        <p id="error" class="hidden dark:text-white text-center">Por favor, completa todos los campos.</p>
        <p id="success" class="hidden dark:text-white text-center">Gracias por contactarme,<br>te responderé lo más pronto posible.</p>
    </div>
</div>