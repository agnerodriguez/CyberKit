document.addEventListener('DOMContentLoaded', () => {
    obtenerDatosProducto();
});

const btnMenos = document.getElementById('menos');
const btnMas = document.getElementById('mas');
const valor = document.getElementById('valor');
const subtotalEl = document.getElementById('subtotalDinamico');

let count = 0;
let precioUnitario = 0;

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
   const mensajeEstado = document.getElementById('mensajeCopiar');

   if (!elemento) {
      console.error(`Error al copiar: No se encontró el elemento con ID '${idElemento}' en el DOM.`);
      if (mensajeEstado) {
         mensajeEstado.innerText = "No se pudo copiar el texto seleccionado.";
         mensajeEstado.style.display = "block";
      }
      return;
   }

   const texto = elemento.innerText;

   navigator.clipboard.writeText(texto)
      .then(() => {
         if (mensajeEstado) {
            mensajeEstado.innerText = "¡Copiado al portapapeles!";
            mensajeEstado.style.color = "#00ff88";
            mensajeEstado.style.display = "block";

            setTimeout(() => {
               mensajeEstado.style.display = "none";
            }, 3000);
         }
      })
      .catch(err => {
         console.error("Detalle técnico del error al intentar copiar:", err);
         
         if (mensajeEstado) {
            mensajeEstado.innerText = "No se pudo copiar el texto. Inténtalo manualmente.";
            mensajeEstado.style.color = "yellow";
            mensajeEstado.style.display = "block";
         }
      });
}

function mostrar(id) { 
    document.querySelectorAll('.contenedor').forEach(contenedor => {
        contenedor.classList.remove('activo');
    });

    const ids = Array.isArray(id) ? id : [id];

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
        id_producto: idProducto
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
            console.error("Error nativo del servidor:", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!response.ok || data.error) {
            if (data.detallesTecnicos) {
                console.error("Detalles técnicos API:", data.detallesTecnicos);
            }
            throw new Error(data.error || "Error al obtener el producto.");
        }

        return data;
    })
    .then(producto => {
        document.getElementById('nombreProducto').textContent = producto.nombre;
        
        precioUnitario = parseFloat(producto.monto) || 0;

        const montoFormateado = precioUnitario.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('precio').textContent = `$${montoFormateado}`;

        document.getElementById('Descripcion').textContent = producto.descripcion;

        const contenedorImagen = document.getElementById('imagen');
        if (contenedorImagen && producto.imagen) {
            contenedorImagen.innerHTML = `<img src="${producto.imagen}" alt="${producto.nombre}">`;
        }

        count = 0;
        valor.textContent = count;
        actualizarSubtotal();
    })
    .catch(error => {
        console.error("Error en la solicitud obtenerDatosProducto():", error);
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
    const inputIdProducto = document.getElementById('idProducto');
    const mensajeComprobante = document.getElementById('mensajeComprobante');

    const idUsuario = inputIdUsuario ? parseInt(inputIdUsuario.value) : 0;
    const idProducto = inputIdProducto ? parseInt(inputIdProducto.value) : 0;
    const cantidad = count;
    const subtotalCalculado = cantidad * precioUnitario;

    if (!inputSubir.files || inputSubir.files.length === 0) {
        if (mensajeComprobante) {
            mensajeComprobante.innerText = "Debes seleccionar una foto del comprobante antes de pagar.";
            mensajeComprobante.style.color = "yellow";
            mensajeComprobante.style.display = "block";
        }
        return;
    }

    if (cantidad <= 0) {
        if (mensajeComprobante) {
            mensajeComprobante.innerText = "Debes seleccionar al menos 1 unidad para comprar.";
            mensajeComprobante.style.color = "yellow";
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
            id_producto: idProducto,
            id_usuario: idUsuario,
            estado: 2,
            cantidad: cantidad,
            subtotal: subtotalCalculado,
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
                console.error("Error nativo del servidor:", textoRespuesta);
                throw new Error("Respuesta no válida del servidor.");
            }

            if (!response.ok || data.error) {
                if (data.detallesTecnicos) {
                    console.error("Detalles técnicos API:", data.detallesTecnicos);
                }
                throw new Error(data.error || "Error al procesar la compra.");
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
            console.error("Error nativo en el catch del envío del pago:", error);
            if (mensajeComprobante) {
                mensajeComprobante.innerText = error.message;
                mensajeComprobante.style.color = "yellow";
                mensajeComprobante.style.display = "block";
            }
        });
    };

    reader.readAsDataURL(archivo);
}