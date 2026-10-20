/**
 * Controla el cambio de vistas y la apertura/cierre del reproductor de videos.
 */

// Oculta las vistas y muestra la indicada por su identificador.
function navigateTo(viewId) {
    const views = document.querySelectorAll('.seccion-vista');
    views.forEach(view => {
        view.classList.remove('activo');
    });

    // Activa el destino y lleva la página al inicio para comenzar la vista.
    const targetView = document.getElementById(viewId);
    if (targetView) {
        targetView.classList.add('activo');
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Completa el modal con los datos seleccionados y lo hace visible.
function openVideoModal(title, description, videoUrl) {
    const modal = document.getElementById('modal-reproduccion-video');
    const modalTitle = document.getElementById('titulo-modal-video');
    const modalDesc = document.getElementById('descripcion-modal-video');
    const modalIframe = document.getElementById('reproductor-iframe-modal');

    if (modal && modalTitle && modalDesc && modalIframe) {
        modalTitle.textContent = title;
        modalDesc.textContent = description;
        modalIframe.src = videoUrl;
        
        modal.classList.add('activo');
    }
}

// Oculta el modal y vacía el iframe para detener la reproducción.
function closeVideoModal() {
    const modal = document.getElementById('modal-reproduccion-video');
    const modalIframe = document.getElementById('reproductor-iframe-modal');

    if (modal) {
        modal.classList.remove('activo');
    }
    if (modalIframe) {
        modalIframe.src = ''; // Detener la reproducción del video al cerrar
    }
}

// Prepara los eventos del menú y del fondo del modal cuando el HTML ya cargó.
document.addEventListener('DOMContentLoaded', () => {
    const btnTutoriales = document.getElementById('enlace-nav-tutoriales');
    if (btnTutoriales) {
        btnTutoriales.addEventListener('click', (e) => {
            e.preventDefault();
            navigateTo('seccion-menu-tutoriales');
        });
    }

    // Un clic fuera del contenido del modal lo cierra; los clics internos no.
    const modal = document.getElementById('modal-reproduccion-video');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeVideoModal();
            }
        });
    }
});


