function Seleccionado(apartado) {
    const elementos = document.querySelectorAll('.nav-lista li');
    elementos.forEach(item => item.classList.remove('activo'));
    apartado.parentElement.classList.add('activo');
}

function menuHamburguesaMovil() {
    const navLista = document.getElementById('nav-lista');
    const hamburguesa = document.getElementById('hamburguesa');

    if (navLista) {
        navLista.classList.toggle('menu-abierto');
    }
    if (hamburguesa) {
        hamburguesa.classList.toggle('abierto');
    }
}