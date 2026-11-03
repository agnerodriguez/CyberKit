
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
// Espera a que la página esté cargada antes de tocar nada
document.addEventListener("DOMContentLoaded", () => {
    // Agarramos los elementos del HTML y los guardamos en variables
    const btnOlvido = document.getElementById("btnOlvido");
    const modalOlvido = document.getElementById("modalOlvido");
    const cerrarModal = document.getElementById("cerrarModal");
    const formOlvido = document.getElementById("formOlvido");
    const mensajeModal = document.getElementById("mensajeModal");

    btnOlvido.addEventListener("click", (e) => {
       // no recargar la página ni saltar arriba
        mensajeModal.textContent = "";    // borra mensajes viejos
        modalOlvido.style.display = "flex"; // muestra la ventana modal
    });

    cerrarModal.addEventListener("click", () => {  // cierra la ventana modal
        modalOlvido.style.display = "none";   
    });

    window.addEventListener("click", (e) => {  // cierra la ventana modal si se hace click fuera de ella
        if (e.target === modalOlvido) {
            modalOlvido.style.display = "none";
        }
    });
    // Maneja el envío del formulario de olvido de contraseña
    formOlvido.addEventListener("submit", async (e) => { 
        e.preventDefault();    // evita que la página se recargue al enviar el formulario

        const emailInput = document.getElementById("emailOlvido").value.trim();  // toma el valor del input de email y le quita espacios al principio y al final

        const formData = new FormData();
        formData.append("email", emailInput); // agrega el email al FormData para enviarlo al servidor

        mensajeModal.style.color = "black";
        mensajeModal.textContent = "Procesando envío...";

        try {
            const respuesta = await fetch("enviarMail.php", { // hace la petición al servidor
                method: "POST",
                body: formData
            });

            const textoRespuesta = await respuesta.text(); // lee la respuesta del servidor como texto

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

