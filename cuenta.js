document.addEventListener('DOMContentLoaded', () => {
    const botonesNavegacion = document.querySelectorAll('.menu-lateral .boton-navegacion[data-target]');
    const vistasCyber = document.querySelectorAll('.vista-cyber');
    const formPerfil = document.getElementById('form-perfil');
    const btnEditar = document.getElementById('btn-editar');
    const btnGuardar = document.getElementById('btn-guardar');
    const camposPerfil = [
        document.getElementById('nombre'),
        document.getElementById('email'),
        document.getElementById('edad'),
        document.getElementById('password')
    ];

    botonesNavegacion.forEach(boton => {
        boton.addEventListener('click', () => {
            const destino = boton.getAttribute('data-target');

            botonesNavegacion.forEach(item => {
                item.classList.remove('activo');
                const icono = item.querySelector('.circulo-flecha i');
                if (icono) icono.className = 'bi bi-chevron-right';
            });

            boton.classList.add('activo');
            const icono = boton.querySelector('.circulo-flecha i');
            if (icono) icono.className = 'bi bi-chevron-down';

            vistasCyber.forEach(vista => {
                vista.classList.toggle('activo', vista.id === destino);
            });
        });
    });

    async function solicitarAPI(payload) {
        const respuesta = await fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json; charset=UTF-8' },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        });

        let datos;
        try {
            datos = await respuesta.json();
        } catch (error) {
            throw new Error('La API devolvió una respuesta inválida.');
        }

        if (!respuesta.ok || !datos || datos.error) {
            throw new Error(datos.error || 'No se pudo completar la solicitud.');
        }

        return datos;
    }

    function cargarUsuario() {
        return solicitarAPI({
            recurso: 'Usuarios',
            consulta: 'ReadUsuarioByID'
        }).then(usuario => {
            document.getElementById('nombre').value = usuario.nombre || '';
            document.getElementById('email').value = usuario.email || '';
            document.getElementById('edad').value = usuario.edad || '';
            document.getElementById('password').value = '';

            const nombreSidebar = document.getElementById('sidebar-username');
            if (nombreSidebar) nombreSidebar.textContent = usuario.nombre || 'Usuario';
        });
    }

    function alternarEdicion(habilitado) {
        camposPerfil.forEach(campo => {
            campo.readOnly = !habilitado;
            campo.classList.toggle('editable-active', habilitado);
        });
        document.getElementById('password').placeholder = habilitado
            ? 'Dejar en blanco para mantenerla'
            : '';
        btnEditar.style.display = habilitado ? 'none' : 'inline-flex';
        btnGuardar.style.display = habilitado ? 'inline-flex' : 'none';
    }

    btnEditar.addEventListener('click', () => {
        alternarEdicion(true);
    });

    formPerfil.addEventListener('submit', async event => {
        event.preventDefault();

        const edad = Number(document.getElementById('edad').value);
        if (!Number.isInteger(edad) || edad < 1 || edad > 120) {
            alert('Ingresá una edad válida entre 1 y 120.');
            return;
        }

        const payload = {
            recurso: 'Usuarios',
            consulta: 'Update',
            nombre: document.getElementById('nombre').value.trim(),
            email: document.getElementById('email').value.trim(),
            edad
        };
        const password = document.getElementById('password').value;
        if (password) payload.contrasena = password;

        btnGuardar.disabled = true;
        try {
            const respuesta = await solicitarAPI(payload);
            alert(respuesta.mensaje || 'Perfil actualizado correctamente.');
            alternarEdicion(false);
            await cargarUsuario();
        } catch (error) {
            console.error('Error al actualizar el perfil:', error);
            alert(error.message);
        } finally {
            btnGuardar.disabled = false;
        }
    });

    async function cargarCompras() {
    const contenedor = document.getElementById('lista-compras');
    contenedor.innerHTML = '<tr><td colspan="6" class="estado-carga">Cargando compras...</td></tr>';

    try {
        const compras = await solicitarAPI({
            recurso: 'DetalleCompra',
            consulta: 'ReadUsuario'
        });

        contenedor.innerHTML = '';

        if (!Array.isArray(compras) || compras.length === 0) {
            contenedor.innerHTML = '<tr><td colspan="6" class="estado-vacio">[ NO HAY COMPRAS REGISTRADAS ]</td></tr>';
            return;
        }

        compras.forEach(compra => {
            const tr = document.createElement('tr');

            // Determinar texto y clase CSS según el estado de la compra
            let claseEstado = 'estado-pendiente';
            let textoEstado = compra.estado || 'Pendiente';
            const idEstado = Number(compra.id_estado);

            if (idEstado === 1 || String(textoEstado).toLowerCase().includes('pago')) {
                claseEstado = 'estado-pago';
                textoEstado = 'Pago';
            } else if (idEstado === 3 || String(textoEstado).toLowerCase().includes('impago')) {
                claseEstado = 'estado-impago';
                textoEstado = 'Impago';
            } else if (idEstado === 2 || String(textoEstado).toLowerCase().includes('pendiente')) {
                claseEstado = 'estado-pendiente';
                textoEstado = 'Pendiente';
            }

            // Formatear precio
            const subtotalFormateado = new Intl.NumberFormat('es-AR', {
                style: 'currency',
                currency: 'ARS'
            }).format(Number(compra.subtotal) || 0);

            // Formatear fecha
            const fechaCompra = new Date(String(compra.fecha || '').replace(' ', 'T'));
            const fechaFormateada = Number.isNaN(fechaCompra.getTime())
                ? (compra.fecha || '-')
                : fechaCompra.toLocaleDateString('es-AR');

            tr.innerHTML = `
                <td>#${compra.id_detalleCompra}</td>
                <td><strong>${escapeHtml(compra.producto || 'Producto')}</strong></td>
                <td>x${compra.cantidad}</td>
                <td>${subtotalFormateado}</td>
                <td>${fechaFormateada}</td>
                <td><span class="insignia-estado ${claseEstado}">${escapeHtml(textoEstado)}</span></td>
            `;

            contenedor.appendChild(tr);
        });
    } catch (error) {
        console.error('Error al cargar las compras:', error);
        contenedor.innerHTML = '<tr><td colspan="6" class="estado-vacio">No se pudo cargar el historial de compras.</td></tr>';
    }
}

// Función para sanitizar HTML y evitar inyecciones XSS
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

    cargarUsuario().catch(error => {
        console.error('Error al cargar el perfil:', error);
        alert(error.message);
    });
    cargarCompras();
});
