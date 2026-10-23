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

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = {
            recurso: 'Usuarios',
            consulta: 'Create',
            nombre: document.getElementById('nombre').value.trim(),
            email: document.getElementById('email').value.trim(),
            edad: document.getElementById('edad').value.trim(),
            contrasena: document.getElementById('password').value
        };

        try {
            const res = await fetch('API.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await res.json();

            if (res.ok) {
                // Éxito: redirigir o mostrar mensaje
                alert('Usuario creado correctamente');
                window.location.href = 'inicioSesion.php';
            } else {
                // Error controlado por la API
                alert(data.error || 'Ocurrió un error');
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión con el servidor');
        }
    });
});