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

    // se vuelva a consultar a la API.php y se rendericen los datos más recientes para los graficos.
    if (id === 'verPagos') {
        cargarPagos();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarProductosAdmin();
    cargarPagos();
});

// ==============================
// PRODUCTOS
// ==============================

function cargarProductosAdmin() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'Productos', consulta: 'ReadAdmin' })
    })
    .then(res => res.json())
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
    .catch(err => console.error("Error al cargar productos:", err));
}

// Cargar datos en el formulario para editar
function prepararEdicion(prod) {
    document.getElementById('prodId').value = prod.id_producto;
    document.getElementById('prodNombre').value = prod.nombre;
    document.getElementById('prodDescripcion').value = prod.descripcion;
    document.getElementById('prodStock').value = prod.stock;
    document.getElementById('prodMonto').value = prod.monto;
    document.getElementById('tituloFormProducto').textContent = "Editar Producto #" + prod.id_producto;
    document.getElementById('btnCancelarEdicion').style.display = 'inline-block';
}

// Agregar / Editar
function guardarProducto() {
    const id = document.getElementById('prodId').value;
    const nombre = document.getElementById('prodNombre').value.trim();
    const descripcion = document.getElementById('prodDescripcion').value.trim();
    const stock = parseInt(document.getElementById('prodStock').value) || 0;
    const monto = parseFloat(document.getElementById('prodMonto').value) || 0;
    const archivoInput = document.getElementById('prodImagen');

    if (!nombre) {
        alert("El nombre del producto es obligatorio.");
        return;
    }
    const esEdicion = id !== "";

    // Función interna encargada de estructurar el payload y hacer la petición
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
        .then(res => res.json())
        .then(data => {
            if (data.mensaje) {
                alert(data.mensaje);
                limpiarFormularioProducto();
                cargarProductosAdmin(); // Refresca la tabla del panel al instante
            } else {
                alert("Error: " + (data.error || "No se pudo procesar la solicitud"));
            }
        })
        .catch(err => console.error("Error en la petición:", err));
    };

    // Si el administrador selecciona una imagen nueva, se procesa a Base64; de lo contrario se envía null
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

// Limpiar formulario
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

// Eliminar producto
function eliminarProducto(id) {
    if (!confirm("¿Eliminar este producto?")) return;

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Productos',
            consulta: 'Delete',
            id_producto: parseInt(id)
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.mensaje) {
            alert(data.mensaje);
            cargarProductosAdmin();
        } else {
            alert(data.error || "Error al eliminar.");
        }
    });
}

// ==============================
// PAGOS
// ==============================

let todosLosPagos = []; // Variable global que almacena todos los registros de pagos

// Función para buscar los datos de compras al servidor
function cargarPagos() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'DetalleCompra', consulta: 'Read' })
    })
    .then(response => response.json())
    .then(data => {
        if (!Array.isArray(data)) {
            throw new Error('La API no devolvió una lista de pagos válida.');
        }
        todosLosPagos = data; 
        mostrarPagos(todosLosPagos); 
    })
    .catch(error => {
        const tbody = document.getElementById('tablapagos');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color: red;">Error al cargar los pagos.</td></tr>';
        }
        console.error(error);
    });
}

// Función que renderiza la tabla con los datos de detalleCompra
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

        // Muestra la imagen del comprobante (Base64) en lugar de la foto del producto
        let comprobanteHTML = '<span style="color: #94a3b8;">Sin imagen</span>';
        if (pago.comprobante) {
            comprobanteHTML = `
                <img src="${escapeHtml(pago.comprobante)}" alt="Comprobante" class="comprobante" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="window.open('${escapeHtml(pago.comprobante)}')">
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

// Función para cambiar el estado del pago en la base de datos
function cambiarEstadoPago(idDetalleCompra, idEstado) {
    const mensaje = document.getElementById('mensajePagos');
    
    // Actualiza el estado mediante la API
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
    .then(res => res.json())
    .then(res => {
        if (res.mensaje) {
            const pagoEncontrado = todosLosPagos.find(p => p.id_detalleCompra == idDetalleCompra);
            if (pagoEncontrado) {
                pagoEncontrado.id_estado = parseInt(idEstado);
            }
            
            if (mensaje) {
                mensaje.style.color = '#0d8a42';
                mensaje.textContent = res.mensaje;
            }

            // Si el estado es "Pagado" (1) llama al archivo para enviar el correo
            if (parseInt(idEstado) === 1) {
                fetch('enviar_correo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_detalleCompra: parseInt(idDetalleCompra) })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.mensaje || data.error);
                })
                .catch(err => console.error("Error al enviar correo:", err));
            }

        } else if (res.error) {
            if (mensaje) {
                mensaje.style.color = 'red';
                mensaje.textContent = "Error al actualizar estado del pago.";
            } 
        }
    })
    .catch(err => {
        if (mensaje) {
            mensaje.style.color = 'red';
            mensaje.textContent = 'Error de conexión al actualizar estado.';
        }
        console.error(err);
    });
}

// Función para filtrar los pagos por estado
function filtrarPagos() {
    const selectEstado = document.getElementById('filtroEstado');
    if (!selectEstado) return;
    const valorFiltro = selectEstado.value;

    // Si no se selecciona nada (opción vacia), muestra todos
    if (valorFiltro === "") {
        mostrarPagos(todosLosPagos);
        return;
    }
    const estadoBusqueda = parseInt(valorFiltro);

    // Filtra el array global segun el id_estado
    const pagosFiltrados = todosLosPagos.filter(pago => {
        return parseInt(pago.id_estado) === estadoBusqueda;
    });

    // Renderiza la tabla con los resultados filtrados
    mostrarPagos(pagosFiltrados);
}

// Limpiar filtros de pagos
function limpiarFiltrosPagos() {
    const filtroEstado = document.getElementById('filtroEstado');
    if (filtroEstado) filtroEstado.value = '';
    mostrarPagos(todosLosPagos);
}

// ==============================
// TUTORIALES
// ==============================

document.addEventListener('DOMContentLoaded', () => {
    cargarTutorialesAdmin();
});

// Cargar listado de tutoriales en la tabla del panel
function cargarTutorialesAdmin() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'Tutoriales', consulta: 'Read' })
    })
    .then(res => res.json())
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
                <td><a href="${escapeHtml(tut.archivo)}" target="_blank" style="color: #0d8a42; text-decoration: underline;">Ver enlace</a></td>
                <td>
                    <button type="button" class="btn-accion-editar" onclick='prepararEdicionTutorial(${JSON.stringify(tut)})'>Editar</button>
                    <button type="button" class="btn-accion-eliminar" onclick="eliminarTutorial(${tut.id_tutorial})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    })
    .catch(err => console.error("Error al cargar tutoriales:", err));
}

// Guardar o Actualizar Tutorial
function guardarTutorial() {
    const id = document.getElementById('tutId').value;
    const nombre = document.getElementById('tutNombre').value.trim();
    const descripcion = document.getElementById('tutDescripcion').value.trim();
    const archivo = document.getElementById('tutArchivo').value.trim();

    if (!nombre) {
        alert("El título/nombre del tutorial es obligatorio.");
        return;
    }

    const esEdicion = id !== "";
    const payload = {
        recurso: 'Tutoriales',
        consulta: esEdicion ? 'Update' : 'Create',
        nombre: nombre,
        descripcion: descripcion,
        archivo: archivo
    };

    if (esEdicion) {
        payload.id_tutorial = parseInt(id);
    }

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.mensaje) {
            alert(data.mensaje);
            limpiarFormularioTutorial();
            cargarTutorialesAdmin();
        } else {
            alert("Error: " + (data.error || "No se pudo procesar la solicitud"));
        }
    })
    .catch(err => console.error("Error en la petición de tutoriales:", err));
}

// Cargar datos en el formulario para editar
function prepararEdicionTutorial(tut) {
    document.getElementById('tutId').value = tut.id_tutorial;
    document.getElementById('tutNombre').value = tut.nombre;
    document.getElementById('tutDescripcion').value = tut.descripcion;
    document.getElementById('tutArchivo').value = tut.archivo;
    document.getElementById('tituloFormTutorial').textContent = "Editar Tutorial #" + tut.id_tutorial;
    document.getElementById('btnCancelarEdicionTut').style.display = 'inline-block';
}

// Limpiar formulario de tutoriales
function limpiarFormularioTutorial() {
    document.getElementById('tutId').value = '';
    document.getElementById('tutNombre').value = '';
    document.getElementById('tutDescripcion').value = '';
    document.getElementById('tutArchivo').value = '';
    document.getElementById('tituloFormTutorial').textContent = "Registrar Nuevo Tutorial";
    document.getElementById('btnCancelarEdicionTut').style.display = 'none';
}

// Eliminar tutorial
function eliminarTutorial(id) {
    if (!confirm("¿Estás seguro de eliminar este tutorial?")) return;

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Tutoriales',
            consulta: 'Delete',
            id_tutorial: parseInt(id)
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.mensaje) {
            alert(data.mensaje);
            cargarTutorialesAdmin();
        } else {
            alert(data.error || "Error al eliminar el tutorial.");
        }
    })
    .catch(err => console.error("Error al eliminar:", err));
}