document.addEventListener('DOMContentLoaded', () => {
    const togglePasswordBtn = document.getElementById('togglePassword'); 
    const passwordInput = document.getElementById('password'); 
    
    const iconoOjito = document.querySelector('.icono-ojito'); // Buscamos la clase exacta que está en el HTML: .icono-ojito

    if (togglePasswordBtn && passwordInput && iconoOjito) {
        togglePasswordBtn.addEventListener('click', () => {
            const esPassword = passwordInput.getAttribute('type') === 'password'; // verificamos si el tipo actual es "password"
            
            // Si actualmente es "password", lo pasamos a "text" (para mostrar la contraseña)
            if (esPassword) {
                passwordInput.setAttribute('type', 'text');
                iconoOjito.style.backgroundImage = "url('iconos/ojo-cerrado.png')";
            } else {
                // Si actualmente es "text", lo pasamos a "password" (para ocultar la contraseña)
                passwordInput.setAttribute('type', 'password');
                iconoOjito.style.backgroundImage = "url('iconos/vista.png')";
            }
        });
    }
});