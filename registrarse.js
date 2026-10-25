document.addEventListener('DOMContentLoaded', () => {
    const togglePasswordBtn = document.getElementById('togglePassword'); 
    const passwordInput = document.getElementById('password'); 
    const iconoOjito = document.querySelector('.icono-ojito');

    if (togglePasswordBtn && passwordInput && iconoOjito) {
        togglePasswordBtn.addEventListener('click', () => {
            const esPassword = passwordInput.getAttribute('type') === 'password';
            
            if (esPassword) {
                passwordInput.setAttribute('type', 'text');
                iconoOjito.style.backgroundImage = "url('iconos/ojo-cerrado.png')";
            } else {
                passwordInput.setAttribute('type', 'password');
                iconoOjito.style.backgroundImage = "url('iconos/vista.png')";
            }
        });
    }
});

