document.addEventListener('DOMContentLoaded', () => {
    obtenerDatosProducto();
})

const btnMenos = document.getElementById('menos');
const btnMas = document.getElementById('mas');
const valor = document.getElementById('valor');
const subtotalEl = document.getElementById('subtotalDinamico');

let count = 0;
let precioUnitario = 0; // Variable global para guardar el precio base

function actualizarSubtotal() {
    const subtotal = count * precioUnitario;
    const subtotalFormateado = subtotal.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    if (subtotalEl) {
        subtotalEl.textContent = `Subtotal: $${subtotalFormateado}`;
    }
}

if (btnMas) {
    btnMas.addEventListener('click', () => {
        count++;
        valor.textContent = count;
        actualizarSubtotal();
    });
}

if (btnMenos) {
    btnMenos.addEventListener('click', () => {
        if (count > 0) {
            count--;
            valor.textContent = count;
            actualizarSubtotal();
        }
    });
}

function copiarElemento(idElemento) {
   const elemento = document.getElementById(idElemento);
   const mensajeEstado = document.getElementById('mensajeCopiar'); // Elemento para la UI

   if (!elemento) {
      console.error(`Error al copiar: No se encontró el elemento con ID '${idElemento}' en el DOM.`);
      if (mensajeEstado) {
         mensajeEstado.innerText = "No se pudo copiar el texto seleccionado.";
         mensajeEstado.style.display = "block";
      }
      return;
   }

   const texto = elemento.innerText;

   //navigator.clipboard: Es una API moderna del navegador que da acceso de lectura y escritura al portapapeles del dispositivo
   navigator.clipboard.writeText(texto)
      .then(() => {
         // Mensaje amigable para el usuario en la interfaz
         if (mensajeEstado) {
            mensajeEstado.innerText = "¡Copiado al portapapeles!";
            mensajeEstado.style.color = "#00ff88"; // Opcional: verde éxito
            mensajeEstado.style.display = "block";

            // Ocultar el mensaje automáticamente después de 3 segundos
            setTimeout(() => {
               mensajeEstado.style.display = "none";
            }, 3000);
         }
      })
      .catch(err => {
         // Detalle técnico para el desarrollador
         console.error("Detalle técnico del error al intentar copiar:", err);
         
         // Mensaje amigable para la interfaz
         if (mensajeEstado) {
            mensajeEstado.innerText = "No se pudo copiar el texto. Inténtalo manualmente.";
            mensajeEstado.style.color = "yellow";
            mensajeEstado.style.display = "block";
         }
      });
}

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

function obtenerDatosProducto() {
    const inputIdProducto = document.getElementById('idProducto');
    const idProducto = inputIdProducto ? parseInt(inputIdProducto.value) : 0;
    const mensaje = document.getElementById('mensajeErrorMostrarProducto');

    if (idProducto <= 0) {
        if (mensaje) {
            mensaje.textContent = "Error: ID de producto no recibido.";
            mensaje.style.display = "block";
        }
        return;
    }

    const payload = {
        recurso: "Productos",
        consulta: "ReadProductoByID",
        id_producto: 1 // idProducto
    };

    fetch("API.php", {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=UTF-8" },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const textoRespuesta = await response.text();
        let data;

        try {
            data = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error nativo del servidor (PHP HTML):", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!response.ok) {
            if (data.detallesTecnicos) {
                console.error("Error:", data.detallesTecnicos);
            }
            throw new Error(data.error || "Error al obtener el producto.");
        }

        return data;
    })
    .then(producto => {
        document.getElementById('nombreProducto').textContent = producto.nombre;
        
        // Guardar el precio unitario numérico
        precioUnitario = parseFloat(producto.monto) || 0;

        // Formatear precio de portada
        const montoFormateado = precioUnitario.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('precio').textContent = `$${montoFormateado}`;

        document.getElementById('Descripcion').textContent = producto.descripcion;

        /* ==============================================================================
        *  DESCOMENTAR CUANDO SE CARGUE LA IMAGEN DESDE LA BASE DE DATOS / API:
        * ==============================================================================
        * const contenedorImagen = document.getElementById('imagen');
        * if (contenedorImagen && producto.imagen) {
        *     contenedorImagen.innerHTML = `<img src="${producto.imagen}" alt="${producto.nombre}">`;
        * }
        * ============================================================================== */

        // Resetear contador y subtotal
        count = 0;
        valor.textContent = count;
        actualizarSubtotal();
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
        if (mensaje) {
            mensaje.textContent = error.message;
            mensaje.style.display = 'block';
        }
    });
}

function comprobanteSeleccionado() {
    const input = document.getElementById('subir');
    const texto = document.getElementById('textoSubir');
    
    if (input.files && input.files[0]) {
        texto.innerText = "Imagen cargada: " + input.files[0].name;
    } else {
        texto.innerText = "SUBIR FOTO DEL COMPROBANTE";
    }
}

function enviarPagoAPI() {
    const inputSubir = document.getElementById('subir');
    const inputIdUsuario = document.getElementById('idUsuario');
    const mensajeComprobante = document.getElementById('mensajeComprobante');

    const idUsuario = inputIdUsuario ? parseInt(inputIdUsuario.value) : 0;
    const cantidad = count;
    const subtotalCalculado = cantidad * precioUnitario; // Cálculo directo

    if (!inputSubir.files || inputSubir.files.length === 0) {
        if (mensajeComprobante) {
            mensajeComprobante.innerText = "Debes seleccionar una foto del comprobante antes de pagar.";
            mensajeComprobante.style.display = "block";
        }
        return;
    }

    if (cantidad <= 0) {
        if (mensajeComprobante) {
            mensajeComprobante.innerText = "Debes seleccionar al menos 1 unidad para comprar.";
            mensajeComprobante.style.display = "block";
        }
        return;
    }

    const archivo = inputSubir.files[0];
    const reader = new FileReader();

    reader.onload = function (e) {
        const payload = {
            recurso: "DetalleCompra",
            consulta: "Create",
            id_producto: 1, // idProducto,
            id_usuario: idUsuario,
            cantidad: cantidad,
            subtotal: subtotalCalculado, // Enviamos el subtotal directamente
            comprobante: e.target.result
        };

        fetch("API.php", {
            method: "POST",
            headers: { "Content-Type": "application/json; charset=UTF-8" },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const textoRespuesta = await response.text();
            let data;

            try {
                data = JSON.parse(textoRespuesta);
            } catch (err) {
                console.error("Error:", textoRespuesta);
                throw new Error("Respuesta no válida del servidor.");
            }

            if (!response.ok) {
                if (data.detallesTecnicos) {
                    console.error("Detalles técnicos API:", data.detallesTecnicos);
                }
                throw new Error(data.error || "Error al procesar el registro del pago.");
            }

            return data;
        })
        .then(data => {
            if (mensajeComprobante) {
                mensajeComprobante.innerText = data.mensaje || "Pago registrado con éxito.";
                mensajeComprobante.style.color = "#00ff88";
                mensajeComprobante.style.display = "block";
            }
        })
        .catch(error => {
            console.error("Error en el envío del pago:", error);
            if (mensajeComprobante) {
                mensajeComprobante.innerText = error.message;
                mensajeComprobante.style.color = "yellow";
                mensajeComprobante.style.display = "block";
            }
        });
    };

    reader.readAsDataURL(archivo);
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