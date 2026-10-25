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

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');

    if (!form) return;

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

            const textoRespuesta = await res.text();
            let data;

            try {
                data = JSON.parse(textoRespuesta);
            } catch (jsonErr) {
                console.error("Respuesta no válida del servidor:", textoRespuesta);
                alert("Error en el formato de respuesta del servidor.");
                return;
            }

            if (res.ok) {
                alert('Usuario creado correctamente');
                window.location.href = 'inicioSesion.php';
            } else {
                alert(data.error || 'Ocurrió un error al registrarse.');
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión con el servidor.');
        }
    });
});