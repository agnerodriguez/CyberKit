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

// OBTENER USUARIO DE LA SESIÓN
// Recupera el identificador y el nombre del usuario almacenados en el navegador.
function getUsuarioSesion() {
    let idUsuario = localStorage.getItem('idUsuario');
    let nombreUsuario = localStorage.getItem('usuario');

// un ID predeterminado si no existe o no es un número válido. ayudaaaaa
    if (!idUsuario || isNaN(idUsuario)) idUsuario = 0; 

    // Asigna un nombre predeterminado si no hay uno almacenado.
    if (!nombreUsuario) nombreUsuario = 'CyberUser';

    // Devuelve los datos del usuario en un objeto.
    return {
        id: parseInt(idUsuario),
        nombre: nombreUsuario
    };
}

// CARGAR PUBLICACIONES
// Obtiene las publicaciones de la categoría seleccionada y las muestra en el foro.
function cargarPublicaciones() {
    // Recupera el identificador de la categoría seleccionada
    const idCategoria = document.getElementById('selectCategoria').value;

    // Obtiene el contenedor donde se mostrarán las publicaciones.
    const contenedor = document.getElementById('preguntasLista');
    if (!contenedor) return;
    
    // Muestra un mensaje mientras se cargan los datos
    contenedor.innerHTML = '<p class="cargando">Cargando publicaciones...</p>';

    // Envía una solicitud POST a la API para consultar las publicaciones.
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'Publicacion',
            consulta: 'Read',
            id_categoria: parseInt(idCategoria)
        })
    })
    // Convierte la respuesta del servidor a formato JSON
    .then(respuesta => respuesta.json())
    .then(publicaciones => {
        // Limpia el contenedor antes de mostrar los resultados
        contenedor.innerHTML = '';

        // Comprueba si no se recibieron publicaciones válidas
        if (!Array.isArray(publicaciones) || publicaciones.length === 0) {
            contenedor.innerHTML = '<p class="sin-datos">No hay publicaciones en esta sección todavía. ¡Sé el primero en participar!</p>';
            return;
        }

        // Recorre las publicaciones recibidas y crea una tarjeta para cada una
        publicaciones.forEach(post => {
            contenedor.appendChild(renderizarPublicacion(post));
        });
    })
    // Maneja los errores que ocurran durante la solicitud
    .catch(error => {
        console.error('Error al cargar publicaciones:', error);
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

    // Verifica que el contenido no este vacio
    if (!contenido) {
        alert('Por favor escribe algo antes de publicar.');
        return;
    }

    
    // Comprueba si se seleccionó una imagen para adjuntar
    if (archivoInput && archivoInput.files && archivoInput.files[0]) {
        // Crea un lector para procesar el archivo seleccionado
        const reader = new FileReader();

        // Se ejecuta cuando termina la lectura de la imagen
        reader.onload = function(e) {
            // Obtiene la cadena Base64 de la imagen.
            const base64String = e.target.result.split(',')[1];

            // Envía los datos de la publicación junto con la imagen
            enviarPostAPI(usuario.id, parseInt(idCategoria), contenido, base64String);
        };

        // Lee el archivo como una URL de datos
        reader.readAsDataURL(archivoInput.files[0]);
    } else {
        // Si no se seleccionó una imagen, envía la publicación sin ella
        enviarPostAPI(usuario.id, parseInt(idCategoria), contenido, null);
    }
}

// ENVIAR PUBLICACIÓN A LA API
// Recibe los datos de la publicación y los envía al servidor para guardarlos
function enviarPostAPI(idUsuario, idCategoria, contenido, imagenBase64) {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        // Convierte los datos de la publicación a formato JSON
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
        // Obtiene los datos JSON y conserva el estado de la respuesta HTTP
        return respuesta.json().then(data => {
            return { ok: respuesta.ok, data: data };
        });
    })
    .then(resultado => {
        // Comprueba si la operación fue exitosa y la API no devolvió un error
        if (resultado.ok && !resultado.data.error) {
            // Limpia el campo de contenido después de publicar.
            document.getElementById('nuevoPostContenido').value = '';

            // Limpia el campo de imagen si existe.
            const archivoInput = document.getElementById('nuevoPostImagen');
            if (archivoInput) archivoInput.value = '';
            
            // Selecciona la categoría de la publicación recién creada
            document.getElementById('selectCategoria').value = idCategoria;

            // Actualiza la lista de publicaciones
            cargarPublicaciones();
        } else {
            // Muestra el mensaje de error devuelto por la API
            alert(resultado.data.error || 'No se pudo crear la publicación.');
        }
    })
    // Maneja errores de conexión o procesamiento
    .catch(error => {
        console.error('Error al crear publicación:', error);
        alert('Error de conexión.');
    });
}

// CARGAR COMENTARIOS
// Consulta los comentarios de una publicación y los muestra en pantalla
function cargarComentarios(idPublicacion) {
    // Obtiene el contenedor de comentarios correspondiente a la publicación
    const listaComentarios = document.getElementById(`comentarios-lista-${idPublicacion}`);
    if (!listaComentarios) return;

    // Solicita a la API los comentarios asociados a la publicación
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
        // Limpia el contenedor antes de mostrar los comentarios actualizados
        listaComentarios.innerHTML = '';

        // Comprueba que la respuesta contenga comentarios
        if (Array.isArray(comentarios) && comentarios.length > 0) {
            // Recorre los comentarios recibidos
            comentarios.forEach(com => {
                // Crea un elemento HTML para cada comentario
                const item = document.createElement('div');

                // Define los estilos visuales del comentarios
                item.style.margin = '6px 0';
                item.style.padding = '6px';
                item.style.background = 'rgba(0,0,0,0.1)';
                item.style.borderRadius = '4px';

                // Inserta el nombre del autor y el contenido del comentario
                // escapeHtml protege el texto para que no se interprete como HTML
                item.innerHTML = `
                    <strong style="font-size: 13px;">${escapeHtml(com.autor_nombre)}:</strong> <span style="font-size: 13px;">${escapeHtml(com.contenido)}</span>
                `;

                // Agrega el comentario al contenedor
                listaComentarios.appendChild(item);
            });
        }
    })
    // Registra cualquier error ocurrido al consultar los comentarios
    .catch(error => {
        console.error('Error al cargar comentarios:', error);
    });
}

// CREAR COMENTARIO
// Obtiene el texto escrito por el usuario y lo envía como respuesta a la publicación indicada
function crearComentario(idPublicacion) {
    // Obtiene el campo de texto correspondiente a la publicación.
    const input = document.getElementById(`input-comentario-${idPublicacion}`);

    // Recupera el contenido y elimina los espacios de los extremos
    const contenido = input.value.trim();

    // Obtiene los datos del usuario que realiza el comentario
    const usuario = getUsuarioSesion();

    // Evita enviar comentarios vacios
    if (!contenido) return;

    // Envía el comentario a la API mediante una solicitud POST
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
        // Convierte la respuesta a JSON y conserva el estado HTTP
        return respuesta.json().then(data => {
            return { ok: respuesta.ok, data: data };
        });
    })
    .then(resultado => {
        // Comprueba si el comentario se creó correctamente
        if (resultado.ok && !resultado.data.error) {
            // Limpia el campo de texto después del envío
            input.value = '';

            // Vuelve a cargar los comentarios para mostrar el nuevo
            cargarComentarios(idPublicacion);
        } else {
            // Informa si la API devolvio un error
            alert(resultado.data.error || 'No se pudo enviar el comentario.');
        }
    })

    // Registra errores ocurridos al enviar la respuesta
    .catch(error => {
        console.error('Error al responder:', error);
    });
}

// RENDERIZAR TARJETA DE PUBLICACIÓN
// Construye el elemento visual que contiene los datos de una publicación, su autor, su fecha, su imagen y la sección para responder.
function renderizarPublicacion(post) {
    // Crea el contenedor principal de la publicación
    const card = document.createElement('div');
    card.className = 'post-card';

    // Define los estilos visuales de la tarjeta
    card.style.background = 'rgba(255,255,255,0.05)';
    card.style.padding = '15px';
    card.style.borderRadius = '8px';
    card.style.marginBottom = '15px';

    // Inicializa el contenido HTML de la imagen
    let imagenHTML = '';

    // Si la publicación tiene una imagen, prepara su etiqueta HTML
    if (post.imagen) {
        imagenHTML = `<div style="margin: 10px 0;"><img src="data:image/jpeg;base64,${post.imagen}" style="max-width: 100%; max-height: 350px; border-radius: 6px;"></div>`;
    }

    // Construye el contenido de la tarjeta con los datos recibidos
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

    // Carga los comentarios correspondientes a esta publicación
    cargarComentarios(post.id_publicacion);

    // Devuelve la tarjeta creada para que pueda agregarse al foro
    return card;
}

// ALTERNAR VISIBILIDAD DEL FORMULARIO
// Muestra u oculta el formulario para crear una publicación
function alternarFormulario() {
    // Obtiene el contenedor del formulario
    const form = document.getElementById('contenedorFormularioPost');

    // Comprueba que el formulario exista
    if (form) {
        // Si está oculto o no tiene un display definido, lo muestra
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'flex';
        } else {
            // Si esta visible, lo oculta
            form.style.display = 'none';
        }
    }
}