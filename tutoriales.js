function navigateTo(viewId) {
    const views = document.querySelectorAll('.seccion-vista');
    views.forEach(view => {
        view.classList.remove('activo');
    });

    const targetView = document.getElementById(viewId);
    if (targetView) {
        targetView.classList.add('activo');
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

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

function closeVideoModal() {
    const modal = document.getElementById('modal-reproduccion-video');
    const modalIframe = document.getElementById('reproductor-iframe-modal');

    if (modal) {
        modal.classList.remove('activo');
    }
    if (modalIframe) {
        modalIframe.src = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const btnTutoriales = document.getElementById('enlace-nav-tutoriales');
    if (btnTutoriales) {
        btnTutoriales.addEventListener('click', (e) => {
            e.preventDefault();
            navigateTo('seccion-menu-tutoriales');
        });
    }

    const modal = document.getElementById('modal-reproduccion-video');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeVideoModal();
            }
        });
    }
});

// Actualiza la barra de progreso de la galería según el scroll de la página
window.addEventListener('scroll', () => {
    const ventanaAltura = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    if (ventanaAltura > 0) {
        const scrollActual = window.scrollY;
        const porcentaje = (scrollActual / ventanaAltura) * 100;
        
        const barraProgreso = document.querySelector('.progreso-videos');
        if (barraProgreso) {
            barraProgreso.style.width = porcentaje + '%';
        }
    }
});