const btnMenos = document.getElementById('menos');
const btnMas = document.getElementById('mas');
const valor = document.getElementById('valor');

let count = 0;

btnMas.addEventListener('click', () => {
  count++;
  valor.textContent = count;
});

btnMenos.addEventListener('click', () => {
  count--;
  valor.textContent = count;
});

function mostrar(id) { 
    // Oculta todos los contenedores
    document.querySelectorAll('.contenedor').forEach(contenedor => {
        contenedor.classList.remove('activo');
    });

    // Convierte "id" en un arreglo
    const ids = Array.isArray(id) ? id : [id];

    // Muestra el contenedor correspondiente añadiendo 'activo'
    ids.forEach(idActual => {
        const elemento = document.getElementById(idActual);
        if (elemento) {
            elemento.classList.add('activo');
        }
    });
}

function notificarEstadoCompra(idDetalleCompra) {
    const mensaje = document.getElementById("mensajeMail");

    const payload = {
        recurso: "DetalleCompra",
        consulta: "enviarMail",
        id_detalleCompra: idDetalleCompra
    };

    fetch("API.php", {
        method: "POST",
        headers: {"Content-Type": "application/json; charset=UTF-8"},
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.mensaje) {
            mensaje = "Notificación enviada.";
        } else {
            mensaje = "Error al notificar.";
            console.error(data.error);
        }
    })
    .catch(error => {
        mensaje = "Error en la solicitud.";
        console.error(error);
    });
}

// Ejemplo de uso al actualizar el estado
function cambiarEstadoCompra(idDetalleCompra, nuevoEstadoId) {
    const mensaje = document.getElementById("mensajeEstado");

    // Si el estado enviado corresponde a Pago o Impago
    const payload = {
        recurso: "DetalleCompra",
        consulta: "Update",
        id_detalleCompra: idDetalleCompra,
        id_estado: nuevoEstadoId
    };

    fetch("API.php", {
        method: "POST",
        headers: {"Content-Type": "application/json; charset=UTF-8"},
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.mensaje) {
            mensaje = "Te informaremos cuando el pago haya sido validado";
            // ADMIN notificarEstadoCompra(idDetalleCompra);
        }
    })
    .catch(error => {
        mensaje = "Error al procesar la compra";
        console.error(error);
    });
}