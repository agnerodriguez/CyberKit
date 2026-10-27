const API_URL = 'API.php'; 

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function getUsuarioSesion() {
    let idUsuario = localStorage.getItem('idUsuario');
    let nombreUsuario = localStorage.getItem('usuario');

    if (!idUsuario || isNaN(idUsuario)) idUsuario = 1; 
    if (!nombreUsuario) nombreUsuario = 'CyberUser';

    return {
        id: parseInt(idUsuario),
        nombre: nombreUsuario
    };
}

document.addEventListener('DOMContentLoaded', () => {
    cargarPublicaciones();
});

// CARGAR PUBLICACIONES
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
    .then(respuesta => respuesta.json())
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
        console.error('Error al cargar publicaciones:', error);
        contenedor.innerHTML = '<p class="error">Ocurrió un error al conectar con el servidor.</p>';
    });
}

// CREAR NUEVA PUBLICACIÓN
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
            const base64String = e.target.result.split(',')[1];
            enviarPostAPI(usuario.id, parseInt(idCategoria), contenido, base64String);
        };
        reader.readAsDataURL(archivoInput.files[0]);
    } else {
        enviarPostAPI(usuario.id, parseInt(idCategoria), contenido, null);
    }
}

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
    .then(respuesta => {
        return respuesta.json().then(data => {
            return { ok: respuesta.ok, data: data };
        });
    })
    .then(resultado => {
        if (resultado.ok && !resultado.data.error) {
            document.getElementById('nuevoPostContenido').value = '';
            const archivoInput = document.getElementById('nuevoPostImagen');
            if (archivoInput) archivoInput.value = '';
            
            document.getElementById('selectCategoria').value = idCategoria;
            cargarPublicaciones();
        } else {
            alert(resultado.data.error || 'No se pudo crear la publicación.');
        }
    })
    .catch(error => {
        console.error('Error al crear publicación:', error);
        alert('Error de conexión.');
    });
}

// RENDERIZAR CARD DE PUBLICACIÓN
function renderizarPublicacion(post) {
    const card = document.createElement('div');
    card.className = 'post-card';
    card.style.background = 'rgba(255,255,255,0.05)';
    card.style.padding = '15px';
    card.style.borderRadius = '8px';
    card.style.marginBottom = '15px';

    let imagenHTML = '';
    if (post.imagen) {
        imagenHTML = `<div style="margin: 10px 0;"><img src="data:image/jpeg;base64,${post.imagen}" style="max-width: 100%; max-height: 350px; border-radius: 6px;"></div>`;
    }

    card.innerHTML = `
        <div class="post-header" style="display: flex; justify-content: space-between; font-size: 13px; color: #aaa; margin-bottom: 8px;">
            <span class="post-autor">👤 ${escapeHtml(post.autor_nombre || 'Anónimo')}</span>
            <span class="post-fecha">🕒 ${escapeHtml(post.fecha || 'Reciente')}</span>
        </div>
        <p class="post-contenido" style="font-size: 16px; margin-bottom: 10px;">${escapeHtml(post.contenido)}</p>
        ${imagenHTML}
        
        <div class="comentarios-seccion" style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
            <div id="comentarios-lista-${post.id_publicacion}" class="comentarios-lista" style="margin-bottom: 10px;"></div>
            <div class="crear-comentario-box" style="display: flex; gap: 8px;">
                <input type="text" id="input-comentario-${post.id_publicacion}" placeholder="Escribe una respuesta..." style="flex: 1; padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                <button class="btn-cyber-sm" onclick="crearComentario(${post.id_publicacion})" style="padding: 6px 12px;">Responder</button>
            </div>
        </div>
    `;

    cargarComentarios(post.id_publicacion);
    return card;
}

// CARGAR COMENTARIOS
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
    .then(respuesta => respuesta.json())
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
                    <strong style="font-size: 13px;">${escapeHtml(com.autor_nombre)}:</strong> <span style="font-size: 13px;">${escapeHtml(com.contenido)}</span>
                `;
                listaComentarios.appendChild(item);
            });
        }
    })
    .catch(error => {
        console.error('Error al cargar comentarios:', error);
    });
}

// CREAR COMENTARIO
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
    .then(respuesta => {
        return respuesta.json().then(data => {
            return { ok: respuesta.ok, data: data };
        });
    })
    .then(resultado => {
        if (resultado.ok && !resultado.data.error) {
            input.value = '';
            cargarComentarios(idPublicacion);
        } else {
            alert(resultado.data.error || 'No se pudo enviar el comentario.');
        }
    })
    .catch(error => {
        console.error('Error al responder:', error);
    });
}

// Función para mostrar/ocultar el formulario al presionar el botón de forma limpia
function alternarFormulario() {
    const form = document.getElementById('contenedorFormularioPost');
    if (form) {
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'flex';
        } else {
            form.style.display = 'none';
        }
    }
}