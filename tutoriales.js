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

// Función que se ejecuta al hacer clic en cualquier tarjeta del while de PHP
function openVideoModal(id_tutorial) {
    const modal = document.getElementById('modal-reproduccion-video');
    const modalTitle = document.getElementById('titulo-modal-video');
    const modalDesc = document.getElementById('descripcion-modal-video');
    const modalIframe = document.getElementById('reproductor-iframe-modal');

    if (modal && modalTitle && modalDesc && modalIframe) {
        modalTitle.textContent = "Cargando...";
        modalDesc.textContent = "Cargando información...";
        modalIframe.src = "";
        modal.classList.add('activo');

        // Petición a la API para obtener los datos específicos (nombre, descripción y link) al abrir el modal
        fetch('API.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                recurso: 'Tutoriales',
                consulta: 'ReadByID',
                id_tutorial: id_tutorial
            })
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data && !data.error) {
                    modalTitle.textContent = data.nombre;
                    modalDesc.textContent = data.descripcion;
                    
                    let urlVideo = data.archivo;
                    if (urlVideo.includes("youtu.be/")) {
                        let idVideo = urlVideo.split("youtu.be/")[1].split("?")[0];
                        urlVideo = `https://www.youtube.com/embed/${idVideo}`;
                    } else if (urlVideo.includes("watch?v=")) {
                        let idVideo = urlVideo.split("watch?v=")[1].split("&")[0];
                        urlVideo = `https://www.youtube.com/embed/${idVideo}`;
                    }
                    
                    modalIframe.src = urlVideo;
                } else {
                    modalTitle.textContent = "Error";
                    modalDesc.textContent = data.error || "No se encontró el tutorial.";
                }
            } catch (e) {
                console.error("Respuesta no JSON de la API:", text);
                modalTitle.textContent = "Error de servidor";
                modalDesc.textContent = "El servidor devolvió una respuesta no válida.";
            }
        });
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

document.addEventListener('DOMContentLoaded', () => {
    const rejilla = document.querySelector('.rejilla-videos-tutoriales');
    if (!rejilla) return;

    let isDown = false;
    let startX;
    let scrollLeft;

    // Al presionar el botón del mouse sobre la lista
    rejilla.addEventListener('mousedown', (e) => {
        isDown = true;
        rejilla.style.cursor = 'grabbing';
        startX = e.pageX - rejilla.offsetLeft;
        scrollLeft = rejilla.scrollLeft;
    });

    // Al soltar o salir de la zona con el mouse
    rejilla.addEventListener('mouseleave', () => {
        isDown = false;
        rejilla.style.cursor = 'grab';
    });

    rejilla.addEventListener('mouseup', () => {
        isDown = false;
        rejilla.style.cursor = 'grab';
    });

    // Al mover el mouse arrastrando
    rejilla.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - rejilla.offsetLeft;
        const walk = (x - startX) * 2; // Factor de velocidad del arrastre
        rejilla.scrollLeft = scrollLeft - walk;
    });
});