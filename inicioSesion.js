
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

    // Selección de elementos del DOM por sus IDs
    const btnOlvido = document.getElementById("btnOlvido");         // Enlace/Botón "Olvidé mi contraseña"
    const modalOlvido = document.getElementById("modalOlvido");     // Contenedor principal del modal
    const cerrarModal = document.getElementById("cerrarModal");     // Botón 'X' para cerrar el modal
    const formOlvido = document.getElementById("formOlvido");       // Formulario de recuperación de clave
    const mensajeModal = document.getElementById("mensajeModal");   // Párrafo para mostrar respuestas/errores

    // Evento para abrir el modal
    btnOlvido.addEventListener("click", (e) => {
        e.preventDefault();                // Evita que la página recargue o navegue al hacer clic en el enlace
        mensajeModal.textContent = "";     // Limpia mensajes previos
        modalOlvido.style.display = "flex"; // Muestra la ventana modal usando flexbox
    });

    // Evento para cerrar el modal al hacer clic en la 'X'
    cerrarModal.addEventListener("click", () => {
        modalOlvido.style.display = "none"; // Oculta el modal
    });

    // Evento para cerrar el modal al hacer clic en el fondo oscuro (fuera del cuadro blanco)
    window.addEventListener("click", (e) => {
        if (e.target === modalOlvido) {
            modalOlvido.style.display = "none"; // Oculta el modal si el clic fue en el overlay
        }
    });

    // Evento para procesar el envío del formulario mediante Fetch (AJAX)
    formOlvido.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("contOlvido").value.trim();

    const formData = new FormData();
    formData.append("email", email);

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

