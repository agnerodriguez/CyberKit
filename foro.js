// URL del archivo PHP que procesa las solicitudes del foro.
const API_URL = 'API.php'; 

// FUNCIÓN PARA ESCAPAR CARACTERES HTML
// Recibe un texto y reemplaza los caracteres especiales por entidades HTML.
function escapeHtml(str) {
    
    // Si el valor es nulo o indefinido, devuelve una cadena vacía.
    if (str === null || str === undefined) return '';

    // Convierte el valor a texto y reemplaza los caracteres especiales.
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

let idUsuarioActual = 0;

function cargarUsuarioSesion() {
    return fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Usuarios',
            consulta: 'ReadUsuarioByID'
        })
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

        // Si no hay sesión iniciada, es un caso esperado.
        if (response.status === 401) {
            return { id_usuario: 0 };
        }

        if (!response.ok || data.error) {
            if (data.detallesTecnicos) {
                console.error("Detalles técnicos API:", data.detallesTecnicos);
            }
            throw new Error(data.error || "No se pudo obtener el usuario de la sesión.");
        }

        return data;
    })
    .then(usuario => {
        idUsuarioActual = parseInt(usuario.id_usuario) || 0;
    })
    .catch(error => {
        console.error("Error en la solicitud cargarUsuarioSesion():", error);
        idUsuarioActual = 0;
    });
}

function getUsuarioSesion() {
    const nombreUsuario = localStorage.getItem('usuario') || localStorage.getItem('nombre') || 'CyberUser';
    return { id: idUsuarioActual, nombre: nombreUsuario };
}

// CARGAR PUBLICACIONES
// Obtiene las publicaciones de la categoría seleccionada y las muestra en el foro.
function cargarPublicaciones() {
    const idCategoria = document.getElementById('selectCategoria').value;
    const contenedor = document.getElementById('preguntasLista');

    if (!contenedor) return;

    contenedor.innerHTML = '<p class="cargando">Cargando publicaciones...</p>';

    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Publicacion',
            consulta: 'Read',
            id_categoria: parseInt(idCategoria)
        })
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
            throw new Error(data.error || "No se pudieron cargar las publicaciones.");
        }

        return data;
    })
    .then(publicaciones => {
        contenedor.innerHTML = '';

        if (!Array.isArray(publicaciones) || publicaciones.length === 0) {
            contenedor.innerHTML = '<p class="sin-datos">No hay publicaciones en esta sección todavía. ¡Sé el primero en participar!</p>';
            return;
        }

        publicaciones.forEach(post => {
            contenedor.appendChild(renderizarPublicacion(post));
        });
    })
    .catch(error => {
        console.error("Error en la solicitud cargarPublicaciones():", error);
        contenedor.innerHTML = '<p class="error">Ocurrió un error al conectar con el servidor.</p>';
    });
}

// CREAR NUEVA PUBLICACIÓN
// Obtiene el contenido, la categoría, la imagen y los datos del usuario para preparar el envío de una nueva publicación
function crearPublicacion() {
    const contenidoInput = document.getElementById('nuevoPostContenido');
    const contenido = contenidoInput.value.trim();
    const idCategoria = document.getElementById('postCategoriaSelect').value;
    const archivoInput = document.getElementById('nuevoPostImagen');
    const usuario = getUsuarioSesion();

    if (!contenido) {
        alert('Por favor escribe algo antes de publicar.');
        return;
    }
    
    if (archivoInput && archivoInput.files && archivoInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            // e.target.result contiene todo el dataURL (ej: data:image/jpeg;base64,/9j/4AAQ...)
            const base64String = e.target.result;
            enviarPostAPI(usuario.id, parseInt(idCategoria), contenido, base64String);
        };

        reader.readAsDataURL(archivoInput.files[0]);
    } else {
        enviarPostAPI(usuario.id, parseInt(idCategoria), contenido, null);
    }
}

// ENVIAR PUBLICACION A LA API
// Recibe los datos de la publicación y los envía al servidor para guardarlos
function enviarPostAPI(idUsuario, idCategoria, contenido, imagenBase64) {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Publicacion',
            consulta: 'Create',
            id_usuario: idUsuario,
            id_categoria: idCategoria,
            contenido: contenido,
            imagen: imagenBase64
        })
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
            throw new Error(data.error || "No se pudo crear la publicación.");
        }

        return data;
    })
    .then(data => {
        // Limpia el campo de contenido después de publicar.
        document.getElementById('nuevoPostContenido').value = '';

        // Limpia el campo de imagen si existe.
        const archivoInput = document.getElementById('nuevoPostImagen');
        if (archivoInput) archivoInput.value = '';
        
        // Selecciona la categoría de la publicación recién creada
        document.getElementById('selectCategoria').value = idCategoria;

        // Actualiza la lista de publicaciones
        cargarPublicaciones();
    })
    .catch(error => {
        console.error("Error en la solicitud de publicación:", error);
        alert(error.message);
    });
}

// CARGAR COMENTARIOS
// Consulta los comentarios de una publicación y los muestra en pantalla
function cargarComentarios(idPublicacion) {
    const listaComentarios = document.getElementById(`comentarios-lista-${idPublicacion}`);
    
    if (!listaComentarios) return;
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Comentario',
            consulta: 'Read',
            id_publicacion: parseInt(idPublicacion)
        })
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
            throw new Error(data.error || "No se pudieron cargar los comentarios.");
        }

        return data;
    })
    .then(comentarios => {
        listaComentarios.innerHTML = '';

        if (Array.isArray(comentarios) && comentarios.length > 0) {
            comentarios.forEach(com => {
                const item = document.createElement('div');

                item.style.margin = '6px 0';
                item.style.padding = '6px';
                item.style.background = 'rgba(0,0,0,0.1)';
                item.style.borderRadius = '4px';

                item.innerHTML = `
                    <strong style="font-size: 13px;">
                    <img src="iconos/usuarioRosa.png" alt="usuario" style="width: 1%">
                    ${escapeHtml(com.autor_nombre)}
                    :</strong> <span style="font-size: 13px;">${escapeHtml(com.contenido)}</span>
                `;

                listaComentarios.appendChild(item);
            });
        }
    })
    .catch(error => {
        console.error("Error en la solicitud cargarComentarios():", error);
    });
}

// CREAR COMENTARIO
// Obtiene el texto escrito por el usuario y lo envía como respuesta a la publicación indicada
function crearComentario(idPublicacion) {
    const input = document.getElementById(`input-comentario-${idPublicacion}`);
    const contenido = input.value.trim();
    const usuario = getUsuarioSesion();

    if (!contenido) return;

    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Comentario',
            consulta: 'Create',
            id_publicacion: parseInt(idPublicacion),
            id_usuario: usuario.id,
            contenido: contenido
        })
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
            throw new Error(data.error || "No se pudo enviar el comentario.");
        }

        return data;
    })
    .then(data => {
        input.value = '';
        cargarComentarios(idPublicacion);
    })
    .catch(error => {
        console.error("Error al responder:", error);
        alert(error.message);
    });
}

// Construye el elemento visual que contiene los datos de una publicación, su autor, su fecha, su imagen y la sección para responder.
// RENDERIZAR TARJETA DE PUBLICACIÓN
function renderizarPublicacion(post) {
    const card = document.createElement('div');
    card.className = 'post-card';

    // Obtenemos el usuario actual de la sesión
    const usuarioActual = getUsuarioSesion();

    let imagenHTML = '';
    if (post.imagen) {
        imagenHTML = `<div style="margin: 10px 0;"><img src="data:image/jpeg;base64,${post.imagen}" style="max-width: 100%; max-height: 350px; border-radius: 6px;"></div>`;
    }

    // Generar el botón de eliminar solo si el usuario logueado es el autor del post
    let botonEliminarHTML = '';

    if (usuarioActual.id > 0 && parseInt(usuarioActual.id) === parseInt(post.id_usuario)) {
        botonEliminarHTML = `
        <button type="button" class="btn-cyber-sm" onclick="eliminarPublicacion(${post.id_publicacion})"
            style="width: 35px; height: 35px; padding: 5px; flex-shrink: 0;">
            <img src="iconos/papelera-de-reciclaje.png" alt="Eliminar" style="width: 18px; height: 18px;">
        </button>`;
    }

    card.innerHTML = `
        <div class="post-header" style="display: flex; align-items: center; margin-bottom: 8px;">
            <span class="post-autor"> <img src="iconos/usuarioCeleste.png" alt="usuario" style= "width: 2.5%"> ${escapeHtml(post.autor_nombre || 'Anónimo')}</span>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                ${botonEliminarHTML}
                <button class="btn-cyber-sm" onclick="alternarComentarios(${post.id_publicacion})" id="btn-ver-respuestas-${post.id_publicacion}">Ver respuestas</button>
                <span class="post-fecha">🕒 ${escapeHtml(post.fecha || 'Reciente')}</span>
            </div>

        </div>

        <p class="post-contenido" style="font-size: 16px; margin-bottom: 10px;">${escapeHtml(post.contenido)}</p>
        ${imagenHTML}

        <!-- Sección de comentarios oculta por defecto -->
        <div id="comentarios-seccion-${post.id_publicacion}" class="comentarios-seccion" style="display: none; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
            <div id="comentarios-lista-${post.id_publicacion}" class="comentarios-lista" style="margin-bottom: 10px;"></div>
            <div class="crear-comentario-box" style="display: flex; gap: 8px;">
                <input type="text" id="input-comentario-${post.id_publicacion}" placeholder="Escribe una respuesta..." style="flex: 1; padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                <button class="btn-cyber-sm" onclick="crearComentario(${post.id_publicacion})" style="padding: 6px 12px;">Responder</button>
            </div>
        </div>
    `;

    return card;
}

// ELIMINAR PUBLICACIÓN
function eliminarPublicacion(idPublicacion) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta publicación?')) {
        return;
    }

    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Publicacion',
            consulta: 'Delete',
            id_publicacion: parseInt(idPublicacion)
        })
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
            throw new Error(data.error || "No se pudo eliminar la publicación.");
        }

        return data;
    })
    .then(data => {
        cargarPublicaciones();
    })
    .catch(error => {
        console.error('Error al eliminar publicación:', error);
        alert(error.message);
    });
}

// Funcion para mostrar u ocultar la seccion de respuestas de una publi especifica
function alternarComentarios(idPublicacion) {
    const seccion = document.getElementById(`comentarios-seccion-${idPublicacion}`);
    const boton = document.getElementById(`btn-ver-respuestas-${idPublicacion}`);
    
    if (seccion) {
        if (seccion.style.display === 'none' || seccion.style.display === '') {

            // Al abrir por primera vez o volver a desplegar cargam los comentarios anteriores
            seccion.style.display = 'block';
            if (boton) boton.textContent = 'Ocultar respuestas';
            cargarComentarios(idPublicacion);
        } else {

            // Oculta
            seccion.style.display = 'none';
            if (boton) boton.textContent = 'Ver respuestas';
        }
    }
}

// Muestra u oculta el formulario para crear una publicacion
function alternarFormulario() {
    if (idUsuarioActual <= 0) {
        window.location.href = 'inicioSesion.php';
        return;
    }

    const form = document.getElementById('contenedorFormularioPost');
    if (form) {
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'flex';
        } else {
            form.style.display = 'none';
        }
    }
}