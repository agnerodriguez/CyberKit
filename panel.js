function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function mostrar(id) {
    const formulario = document.getElementById(id);
    const inicio = document.getElementById('inicio');

    if (id === 'inicio') {
        document.querySelectorAll('.formulario')
            .forEach(f => f.classList.remove('activo'));
        inicio.classList.add('activo');
        return;
    }

    if (formulario.classList.contains('activo')) {
        formulario.classList.remove('activo');
        inicio.classList.add('activo');
        return;
    }
    document.querySelectorAll('.formulario')
        .forEach(f => f.classList.remove('activo'));
    formulario.classList.add('activo');

    if (id === 'verPagos') {
        cargarPagos();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();
    cargarPagos();
    cargarTutoriales();
});

// ==============================
// PRODUCTOS
// ==============================

function cargarProductos() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'Productos', consulta: 'ReadAdmin' })
    })
    .then(async res => {
        const textoRespuesta = await res.text();
        let productos;

        try {
            productos = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error nativo del servidor:", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!res.ok || productos.error) {
            if (productos.detallesTecnicos) {
                console.error("Detalles técnicos API:", productos.detallesTecnicos);
            }
            throw new Error(productos.error || "No se pudieron cargar los productos.");
        }

        return productos;
    })
    .then(productos => {
        const tbody = document.getElementById('tablaProductosAdmin');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (!Array.isArray(productos) || productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay productos registrados.</td></tr>';
            return;
        }

        productos.forEach(prod => {
            let imagenHTML = '<span style="color: #94a3b8;">Sin imagen</span>';
            if (prod.imagen && prod.imagen !== 'pendiente') {
                imagenHTML = `<img src="${escapeHtml(prod.imagen)}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">`;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${prod.id_producto}</td>
                <td>${imagenHTML}</td>
                <td><strong>${escapeHtml(prod.nombre)}</strong></td>
                <td>${prod.stock}</td>
                <td>$${Number(prod.monto).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn-accion-editar" onclick='prepararEdicion(${JSON.stringify(prod)})'>Editar</button>
                    <button type="button" class="btn-accion-eliminar" onclick="eliminarProducto(${prod.id_producto})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    })
    .catch(err => console.error("Error nativo en cargarProductos():", err));
}

function prepararEdicion(prod) {
    document.getElementById('prodId').value = prod.id_producto;
    document.getElementById('prodNombre').value = prod.nombre;
    document.getElementById('prodDescripcion').value = prod.descripcion;
    document.getElementById('prodStock').value = prod.stock;
    document.getElementById('prodMonto').value = prod.monto;
    document.getElementById('tituloFormProducto').textContent = "Editar Producto #" + prod.id_producto;
    document.getElementById('btnCancelarEdicion').style.display = 'inline-block';
}

function guardarProducto() {
    const id = document.getElementById('prodId').value;
    const nombre = document.getElementById('prodNombre').value.trim();
    const descripcion = document.getElementById('prodDescripcion').value.trim();
    const stock = parseInt(document.getElementById('prodStock').value) || 0;
    const monto = parseFloat(document.getElementById('prodMonto').value) || 0;
    const archivoInput = document.getElementById('prodImagen');
    const mensajePagos = document.getElementById('mensajePagos'); // Elemento compartido para feedback

    if (!nombre) {
        if (mensajePagos) {
            mensajePagos.style.color = 'red';
            mensajePagos.textContent = "El nombre del producto es obligatorio.";
        }
        return;
    }
    const esEdicion = id !== "";

    const procesarEnvio = (imagenBase64) => {
        const payload = {
            recurso: 'Productos',
            consulta: esEdicion ? 'Update' : 'Create',
            nombre: nombre,
            descripcion: descripcion,
            stock: stock,
            monto: monto,
            imagen: imagenBase64
        };

        if (esEdicion) {
            payload.id_producto = parseInt(id);
        }

        fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const textoRespuesta = await res.text();
            let data;

            try {
                data = JSON.parse(textoRespuesta);
            } catch (e) {
                console.error("Error nativo del servidor:", textoRespuesta);
                throw new Error("Respuesta no válida del servidor.");
            }

            if (!res.ok || data.error) {
                if (data.detallesTecnicos) {
                    console.error("Detalles técnicos API:", data.detallesTecnicos);
                }
                throw new Error(data.error || "No se pudo guardar el producto.");
            }

            return data;
        })
        .then(data => {
            if (mensajePagos) {
                mensajePagos.style.color = '#45024b';
                mensajePagos.textContent = data.mensaje || "Producto procesado correctamente.";
            }
            limpiarFormularioProducto();
            cargarProductos();
        })
        .catch(err => {
            console.error("Error nativo en guardarProducto():", err);
            if (mensajePagos) {
                mensajePagos.style.color = 'red';
                mensajePagos.textContent = err.message;
            }
        });
    };

    if (archivoInput.files && archivoInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            procesarEnvio(e.target.result);
        };
        reader.readAsDataURL(archivoInput.files[0]);
    } else {
        procesarEnvio(null);
    }
}

function limpiarFormularioProducto() {
    document.getElementById('prodId').value = '';
    document.getElementById('prodNombre').value = '';
    document.getElementById('prodDescripcion').value = '';
    document.getElementById('prodStock').value = '';
    document.getElementById('prodMonto').value = '';
    document.getElementById('prodImagen').value = '';
    document.getElementById('tituloFormProducto').textContent = "Registrar Nuevo Producto";
    document.getElementById('btnCancelarEdicion').style.display = 'none';
}

function eliminarProducto(id) {
    const mensajePagos = document.getElementById('mensajePagos');

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Productos',
            consulta: 'Delete',
            id_producto: parseInt(id)
        })
    })
    .then(async res => {
        const textoRespuesta = await res.text();
        let data;

        try {
            data = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error nativo del servidor:", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!res.ok || data.error) {
            if (data.detallesTecnicos) {
                console.error("Detalles técnicos API:", data.detallesTecnicos);
            }
            throw new Error(data.error || "Error al eliminar el producto.");
        }

        return data;
    })
    .then(data => {
        if (mensajePagos) {
            mensajePagos.style.color = '#45024b';
            mensajePagos.textContent = data.mensaje || "Producto eliminado correctamente.";
        }
        cargarProductos();
    })
    .catch(err => {
        console.error("Error nativo en eliminarProducto():", err);
        if (mensajePagos) {
            mensajePagos.style.color = 'red';
            mensajePagos.textContent = err.message;
        }
    });
}

// ==============================
// PAGOS
// ==============================

let todosLosPagos = [];

function cargarPagos() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'DetalleCompra', consulta: 'Read' })
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
            throw new Error(data.error || "Error al obtener los pagos.");
        }

        if (!Array.isArray(data)) {
            throw new Error("La API no devolvió una lista de pagos válida.");
        }

        return data;
    })
    .then(data => {
        todosLosPagos = data; 
        mostrarPagos(todosLosPagos); 
    })
    .catch(error => {
        const tbody = document.getElementById('tablapagos');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color: red;">Error al cargar los pagos.</td></tr>';
        }
        console.error("Error nativo en cargarPagos():", error);
    });
}

function mostrarPagos(pagos) {
    const tbody = document.getElementById('tablapagos');
    const mensaje = document.getElementById('mensajeFiltrosPagos');
    if (!tbody) return;
    tbody.innerHTML = ''; 

    if (pagos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align:center;">
                    No se encontraron pagos registrados.
                </td>
            </tr>
        `;
        if (mensaje) {
            mensaje.textContent = 'No hay pagos que coincidan con los filtros.';
        }
        return;
    }

    if (mensaje) {
        mensaje.textContent = `Se encontraron ${pagos.length} pago(s).`;
    }

    pagos.forEach(pago => {
        const fila = document.createElement('tr');

        let comprobanteHTML = '<span style="color: #94a3b8;">Sin imagen</span>';
        if (pago.comprobante) {
            comprobanteHTML = `
                <img src="${escapeHtml(pago.comprobante)}" alt="Comprobante" class="comprobante" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="verComprobante('${escapeHtml(pago.comprobante)}')">
            `;
        }

        const idEstadoActual = parseInt(pago.id_estado);
        const estadoSelect = `
            <select onchange="cambiarEstadoPago(${pago.id_detalleCompra}, this.value)" class="estado">
                <option value="1" ${idEstadoActual === 1 ? 'selected' : ''}>Pagado</option>
                <option value="2" ${idEstadoActual === 2 ? 'selected' : ''}>Pendiente</option>
                <option value="3" ${idEstadoActual === 3 ? 'selected' : ''}>Impago</option>
            </select>
        `;

        fila.innerHTML = `
            <td>#${pago.id_detalleCompra}</td>
            <td>${escapeHtml(pago.fecha || '-')}</td>
            <td><strong>${escapeHtml(pago.usuario ? pago.usuario.nombre : 'Desconocido')}</strong><br><small>${escapeHtml(pago.usuario ? pago.usuario.email : '')}</small></td>
            <td>${escapeHtml(pago.producto ? pago.producto.nombre : '-')} (x${pago.cantidad})</td>
            <td>$${Number(pago.subtotal || 0).toFixed(2)}</td>
            <td>${comprobanteHTML}</td>
            <td>${estadoSelect}</td>
        `;
        tbody.appendChild(fila);
    });
}

function cambiarEstadoPago(idDetalleCompra, idEstado) {
    const mensaje = document.getElementById('mensajePagos');

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'DetalleCompra',
            consulta: 'Update',
            id_detalleCompra: parseInt(idDetalleCompra),
            id_estado: parseInt(idEstado)
        })
    })
    .then(async response => {
        const textoRespuesta = await response.text();
        let res;

        try {
            res = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error nativo del servidor:", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!response.ok || res.error) {
            if (res.detallesTecnicos) {
                console.error("Detalles técnicos API:", res.detallesTecnicos);
            }
            throw new Error(res.error || "Error al actualizar el pago.");
        }

        return res;
    })
    .then(res => {
        const pagoEncontrado = todosLosPagos.find(
            p => p.id_detalleCompra == idDetalleCompra
        );
        if (pagoEncontrado) {
            pagoEncontrado.id_estado = parseInt(idEstado);
        }
        if (mensaje) {
            mensaje.style.color = '#45024b';
            mensaje.textContent = res.mensaje;
        }

        const estadoNum = parseInt(idEstado);

        // Si el estado es Pagado (1) o Impago (3)
        if (estadoNum === 1 || estadoNum === 3) {
            if (mensaje) {
                mensaje.textContent = estadoNum === 1 
                    ? 'Pago aprobado. Generando recibo y enviando correo...' 
                    : 'Estado cambiado a impago. Enviando notificación al cliente...';
            }

            // Selecciona el archivo PHP correspondiente según el estado
            const archivoEnvioMail = (estadoNum === 1) ? 'enviarValidacionMail.php' : 'enviarNotificacionImpago.php';

            fetch(archivoEnvioMail, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_detalleCompra: parseInt(idDetalleCompra)
                })
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
                    throw new Error(data.error || "No se pudo enviar el correo.");
                }

                return data;
            })
            .then(data => {
                if (mensaje) {
                    mensaje.style.color = '#45024b';
                    mensaje.textContent = data.mensaje || 'Estado actualizado y correo enviado correctamente.';
                }
            })
            .catch(error => {
                console.error('Error nativo en envío de correo:', error);
                if (mensaje) {
                    mensaje.style.color = 'red';
                    mensaje.textContent = 'El estado se actualizó, pero falló el envío del correo: ' + error.message;
                }
            });
        }
    })
    .catch(error => {
        console.error('Error nativo al actualizar el pago:', error);

        if (mensaje) {
            mensaje.style.color = 'red';
            mensaje.textContent = error.message;
        }
    });
}

function filtrarPagos() {
    const selectEstado = document.getElementById('filtroEstado');
    if (!selectEstado) return;
    const valorFiltro = selectEstado.value;

    if (valorFiltro === "") {
        mostrarPagos(todosLosPagos);
        return;
    }
    const estadoBusqueda = parseInt(valorFiltro);

    const pagosFiltrados = todosLosPagos.filter(pago => {
        return parseInt(pago.id_estado) === estadoBusqueda;
    });

    mostrarPagos(pagosFiltrados);
}

function limpiarFiltrosPagos() {
    const filtroEstado = document.getElementById('filtroEstado');
    if (filtroEstado) filtroEstado.value = '';
    mostrarPagos(todosLosPagos);
}

function verComprobante(base64) {
    const ventana = window.open();
    ventana.document.write(`
        <html>
            <head><title>Comprobante de Pago - RecyTech</title></head>
            <body style="margin:0; background:#1e1e1e; display:flex; justify-content:center; align-items:center; height:100vh;">
                <img src="${base64}" style="max-width:100%; max-height:100%; object-fit:contain; border-radius: 6px;">
            </body>
        </html>
    `);
}

// ==============================
// TUTORIALES
// ==============================

function cargarTutoriales() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'Tutoriales', consulta: 'Read' })
    })
    .then(async res => {
        const textoRespuesta = await res.text();
        let tutoriales;

        try {
            tutoriales = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error nativo del servidor:", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!res.ok || tutoriales.error) {
            if (tutoriales.detallesTecnicos) {
                console.error(
                    "Detalle técnico API (cargarTutoriales):",
                    tutoriales.detallesTecnicos
                );
            }

            throw new Error(
                tutoriales.error || "No se pudieron obtener los tutoriales."
            );
        }

        return tutoriales;
    })
    .then(tutoriales => {
        const tbody = document.getElementById('tablaTutorialesAdmin');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!Array.isArray(tutoriales) || tutoriales.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No hay tutoriales registrados.</td></tr>';
            return;
        }

        tutoriales.forEach(tut => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>#${tut.id_tutorial}</td>
                <td><strong>${escapeHtml(tut.nombre)}</strong></td>
                <td>${escapeHtml(tut.descripcion || '-')}</td>
                <td>
                    <a href="${escapeHtml(tut.archivo)}" target="_blank" style="color: #45024b; text-decoration: underline;">
                        Ver enlace
                    </a>
                </td>
                <td>
                    <button type="button" class="btn-accion-editar" onclick='prepararEdicionTutorial(${JSON.stringify(tut)})'>Editar</button>
                    <button type="button" class="btn-accion-eliminar" onclick="eliminarTutorial(${tut.id_tutorial})">Eliminar</button>
                </td>
            `;

            tbody.appendChild(tr);
        });
    })
    .catch(error => {
        console.error("Error nativo en cargarTutoriales():", error);
    });
}


function guardarTutorial() {
    const id = document.getElementById('tutId').value;
    const nombre = document.getElementById('tutNombre').value.trim();
    const descripcion = document.getElementById('tutDescripcion').value.trim();
    const enlace = document.getElementById('tutEnlace').value.trim();
    const archivoInput = document.getElementById('tutArchivo');
    const mensajePagos = document.getElementById('mensajePagos');

    if (!nombre) {
        if (mensajePagos) {
            mensajePagos.style.color = 'red';
            mensajePagos.textContent = "El título del tutorial es obligatorio.";
        }
        return;
    }

    const esEdicion = id !== "";

    const enviarDatos = (recursoFinal) => {
        const payload = {
            recurso: 'Tutoriales',
            consulta: esEdicion ? 'Update' : 'Create',
            nombre: nombre,
            descripcion: descripcion,
            archivo: recursoFinal
        };

        if (esEdicion) {
            payload.id_tutorial = parseInt(id);
        }

        fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const textoRespuesta = await res.text();
            let data;

            try {
                data = JSON.parse(textoRespuesta);
            } catch (e) {
                console.error("Error nativo del servidor:", textoRespuesta);
                throw new Error("Respuesta no válida del servidor.");
            }

            if (!res.ok || data.error) {
                if (data.detallesTecnicos) {
                    console.error(
                        "Detalle técnico API (guardarTutorial):",
                        data.detallesTecnicos
                    );
                }

                throw new Error(
                    data.error || "No se pudo procesar el tutorial."
                );
            }

            return data;
        })
        .then(data => {
            if (mensajePagos) {
                mensajePagos.style.color = '#45024b';
                mensajePagos.textContent = data.mensaje || "Tutorial guardado correctamente.";
            }

            limpiarFormularioTutorial();
            cargarTutoriales();
        })
        .catch(err => {
            console.error("Error nativo en guardarTutorial():", err);

            if (mensajePagos) {
                mensajePagos.style.color = 'red';
                mensajePagos.textContent = err.message;
            }
        });
    };

    // Si seleccionaron un archivo físico, lo convertimos a Base64.
    if (archivoInput.files && archivoInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            enviarDatos(e.target.result);
        };

        reader.onerror = function(e) {
            console.error("Error nativo al leer el archivo:", e);

            if (mensajePagos) {
                mensajePagos.style.color = 'red';
                mensajePagos.textContent = "No se pudo leer el archivo seleccionado.";
            }
        };

        reader.readAsDataURL(archivoInput.files[0]);
    } else {
        // Si no seleccionaron archivo, envia el enlace ingresado
        enviarDatos(enlace);
    }
}

function prepararEdicionTutorial(tut) {
    document.getElementById('tutId').value = tut.id_tutorial;
    document.getElementById('tutNombre').value = tut.nombre;
    document.getElementById('tutDescripcion').value = tut.descripcion || '';

    // No se puede asignar una ruta a un input de tipo file
    // Si es un enlace web, lo muestra en el campo de enlace
    if (tut.archivo && tut.archivo.startsWith('http')) {
        document.getElementById('tutEnlace').value = tut.archivo;
    } else {
        document.getElementById('tutEnlace').value = '';
    }

    document.getElementById('tutArchivo').value = '';
    document.getElementById('tituloFormTutorial').textContent = "Editar Tutorial #" + tut.id_tutorial;
    document.getElementById('btnCancelarEdicionTut').style.display = 'inline-block';
}


function limpiarFormularioTutorial() {
    document.getElementById('tutId').value = '';
    document.getElementById('tutNombre').value = '';
    document.getElementById('tutDescripcion').value = '';
    document.getElementById('tutEnlace').value = '';
    document.getElementById('tutArchivo').value = '';
    document.getElementById('tituloFormTutorial').textContent = "Registrar Nuevo Tutorial";
    document.getElementById('btnCancelarEdicionTut').style.display = 'none';
}


function eliminarTutorial(id) {
    const mensajePagos = document.getElementById('mensajePagos');

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Tutoriales',
            consulta: 'Delete',
            id_tutorial: parseInt(id)
        })
    })
    .then(async res => {
        const textoRespuesta = await res.text();
        let data;

        try {
            data = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error nativo del servidor:", textoRespuesta);
            throw new Error("Respuesta no válida del servidor.");
        }

        if (!res.ok || data.error) {
            if (data.detallesTecnicos) {
                console.error(
                    "Detalle técnico API (eliminarTutorial):",
                    data.detallesTecnicos
                );
            }

            throw new Error(
                data.error || "Error al eliminar el tutorial."
            );
        }

        return data;
    })
    .then(data => {
        if (mensajePagos) {
            mensajePagos.style.color = '#45024b';
            mensajePagos.textContent = data.mensaje || "Tutorial eliminado correctamente.";
        }

        cargarTutoriales();
    })
    .catch(err => {
        console.error("Error nativo en eliminarTutorial():", err);

        if (mensajePagos) {
            mensajePagos.style.color = 'red';
            mensajePagos.textContent = err.message;
        }
    });
}