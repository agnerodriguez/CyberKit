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

// Modificado para obtener los datos dinámicamente por la API
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

        // Petición a tu API con ReadByID
        fetch('API.php', { // Reemplaza 'api.php' por la ruta real de tu archivo de API si es diferente
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'ReadByID',
                id_tutorial: id_tutorial
            })
        })
        .then(response => response.text()) // Cambiamos a .text() primero para evitar errores de JSON si PHP arroja warnings
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
                modalDesc.textContent = "El servidor devolvió una respuesta inválida. Revisa la consola.";
            }
        })
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