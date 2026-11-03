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
        contenedor.textContent = 'Cargando compras...';

        try {
            const compras = await solicitarAPI({
                recurso: 'DetalleCompra',
                consulta: 'ReadUsuario'
            });

            contenedor.replaceChildren();
            if (!Array.isArray(compras) || compras.length === 0) {
                contenedor.textContent = '[ NO HAY COMPRAS REGISTRADAS ]';
                contenedor.className = 'lista-compras estado-vacio';
                return;
            }

            contenedor.className = 'lista-compras';
            compras.forEach(compra => {
                const fila = document.createElement('div');
                fila.className = 'purchase-row';

                const info = document.createElement('div');
                info.className = 'purchase-info';

                const icono = document.createElement('div');
                icono.className = 'cart-icon-circle';
                icono.innerHTML = '<i class="bi bi-cart-fill"></i>';

                const etiqueta = document.createElement('span');
                etiqueta.className = 'action-label';
                etiqueta.textContent = 'Compraste:';

                const producto = document.createElement('span');
                producto.className = 'product-name';
                producto.textContent = compra.producto || 'Producto';

                info.append(icono, etiqueta, producto);

                const meta = document.createElement('div');
                meta.className = 'purchase-meta';

                const cantidad = document.createElement('span');
                cantidad.className = 'meta-qty';
                cantidad.textContent = `x${compra.cantidad}`;

                const precio = document.createElement('span');
                precio.className = 'meta-price';
                precio.textContent = new Intl.NumberFormat('es-AR', {
                    style: 'currency',
                    currency: 'ARS'
                }).format(Number(compra.subtotal) || 0);

                const fecha = document.createElement('span');
                fecha.className = 'meta-date';
                const fechaCompra = new Date(String(compra.fecha || '').replace(' ', 'T'));
                fecha.textContent = Number.isNaN(fechaCompra.getTime())
                    ? (compra.fecha || '')
                    : fechaCompra.toLocaleDateString('es-AR');

                meta.append(cantidad, precio, fecha);
                fila.append(info, meta);
                contenedor.append(fila);
            });
        } catch (error) {
            console.error('Error al cargar las compras:', error);
            contenedor.textContent = 'No se pudo cargar el historial de compras.';
            contenedor.className = 'lista-compras estado-vacio';
        }
    }

    cargarUsuario().catch(error => {
        console.error('Error al cargar el perfil:', error);
        alert(error.message);
    });
    cargarCompras();
});
