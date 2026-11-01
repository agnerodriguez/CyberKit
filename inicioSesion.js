
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

document.addEventListener("DOMContentLoaded", () => {
    const btnOlvido = document.getElementById("btnOlvido");
    const modalOlvido = document.getElementById("modalOlvido");
    const cerrarModal = document.getElementById("cerrarModal");
    const formOlvido = document.getElementById("formOlvido");
    const mensajeModal = document.getElementById("mensajeModal");

    btnOlvido.addEventListener("click", (e) => {
        e.preventDefault();
        mensajeModal.textContent = "";
        modalOlvido.style.display = "flex";
    });

    cerrarModal.addEventListener("click", () => {
        modalOlvido.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === modalOlvido) {
            modalOlvido.style.display = "none";
        }
    });

    formOlvido.addEventListener("submit", async (e) => {
        e.preventDefault();

        const emailInput = document.getElementById("emailOlvido").value.trim();

        const formData = new FormData();
        formData.append("email", emailInput);

        mensajeModal.style.color = "black";
        mensajeModal.textContent = "Procesando envío...";

        try {
            const respuesta = await fetch("enviarMail.php", {
                method: "POST",
                body: formData
            });

            const textoRespuesta = await respuesta.text();

            if (respuesta.ok) {
                mensajeModal.style.color = "black";
                mensajeModal.textContent = textoRespuesta;
            } else {
                mensajeModal.style.color = "red";
                mensajeModal.textContent = textoRespuesta || "Ocurrió un error al procesar.";
            }
        } catch (error) {
            mensajeModal.style.color = "red";
            mensajeModal.textContent = "Error de conexión con el servidor.";
        }
    });
});

