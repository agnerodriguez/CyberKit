document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('contenedorProductos');

    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'Productos', consulta: 'ReadUsuario' })
    })
    .then(res => res.json())
    .then(productos => {
        let html = '';
        productos.forEach(p => {
            html += `
                <div class='tarjeta-producto'>
                    <div class='marco-externo'>
                        <div class='marco-interno'>
                            <img src='${p.imagen}' alt='${p.nombre}'>
                        </div>
                    </div>
                    <div class='info'>
                        <h3 class='titulo-producto'>${p.nombre}</h3>
                        <p class='descripcion-producto'>${p.descripcion}</p>
                        <div class='contenedorBtn fuentePrincipal'>
                            <button type='button' class='btnEnviar fuentePrincipal'
                                onclick="comprarProducto(${p.id_producto})">Comprar</button> 
                        </div>
                    </div>
                </div>
            `;
        });
        contenedor.innerHTML = html;
    })
    .catch(err => {
        contenedor.innerHTML = "<p style='color:#ff00ff; text-align:center;'>Error al cargar los productos.</p>";
        console.error('Error:', err);
    });
});

function comprarProducto(idProducto) {
    location = `comprar.php?id=${idProducto}`;
}