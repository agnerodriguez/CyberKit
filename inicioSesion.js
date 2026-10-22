
document.addEventListener('DOMContentLoaded', () => {
    // Referencias únicas con los IDs de inicio de sesión
    const botonOjo = document.getElementById('login-boton-ojo'); 
    const inputPassword = document.getElementById('login-password'); 
    const iconoOjito = document.querySelector('.icono-ojito');

    if (botonOjo && inputPassword && iconoOjito) {
        botonOjo.addEventListener('click', () => {
            const esPassword = inputPassword.getAttribute('type') === 'password'; 
            
            if (esPassword) {
                // Mostrar contraseña
                inputPassword.setAttribute('type', 'text');
                iconoOjito.style.backgroundImage = "url('iconos/ojo-cerrado.png')";
            } else {
                // Ocultar contraseña
                inputPassword.setAttribute('type', 'password');
                iconoOjito.style.backgroundImage = "url('iconos/vista.png')";
            }
        });
    }
});


