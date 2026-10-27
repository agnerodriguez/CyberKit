<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="panel.css">
</head>
<body>
    <div class="contenedor">

        <!-- Sidebar -->
        <aside class="sideBar">
            <div class="logo">
                <a href="inicio.php"><img src="iconos/logo.jpg" alt="Logo"></a>
            </div>
            <nav>
                <button onclick="mostrar('productos')"><img src="iconos/productos.png" alt="moneda" style= "width: 19%">Productos</button>
                <button onclick="mostrar('verPagos')"><img src="iconos/moneda.png" alt="moneda" style= "width: 19%">Ver pagos</button>
                <button onclick="mostrar('tutoriales')"><img src="iconos/video-tutorial.png" alt="" style= "width: 19%">Tutoriales</button>
            </nav>
        </aside>

        <!-- APARTADOS -->
        <main class="contenido">

            <!-- Productos -->
            <div id="productos" class="formulario tarjeta productos-cards">
                
                <!-- Bloque de arriba: formulario de agregar / editar -->
                <div class="card-formulario-producto">
                    <div class="productos-header">
                        <h2 id="tituloFormProducto">Registrar Nuevo Producto</h2>
                    </div>
                    
                    <input type="hidden" id="prodId" value="">
                    
                    <div class="form-producto-grid">
                        <input type="text" id="prodNombre" placeholder="Nombre del producto">
                        <input type="number" id="prodStock" placeholder="Stock disponible">
                        <input type="number" step="0.01" id="prodMonto" placeholder="Precio ($)">
                        <input type="file" id="prodImagen" accept="image/*">
                        <textarea id="prodDescripcion" placeholder="Descripción del producto..."></textarea>
                        
                        <div class="form-acciones">
                            <button type="button" class="btn-guardar" onclick="guardarProducto()">Guardar Producto</button>
                            <button type="button" id="btnCancelarEdicion" class="btn-cancelar" style="display:none;" onclick="limpiarFormularioProducto()">Cancelar</button>
                        </div>
                    </div>
                </div>

                <!-- BLOQUE ABAJO: Tabla con el listado completo de productos -->
                <div class="card-tabla-productos">
                    <h3>Listado de Productos</h3>
                    <div class="tabla-productos-contenedor">
                        <table class="tabla-productos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductosAdmin"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagos -->
            <div id="verPagos" class="formulario tarjeta pagos-card">
                <div class="pagos-header">
                    <h2 class="titulo-pagos">Pagos registrados</h2>
                </div>
                <p id="mensajePagos" class="msj-error"></p>

                <!-- Filtros de pagos -->
                <div class="filtros-pagos">
                    <div class="filtro-grupo">
                        <label for="filtroEstado">Estado:</label>
                        <select id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="1">Pagado</option>
                            <option value="2">Pendiente</option>
                            <option value="3">Impago</option>
                        </select>
                    </div>
                    <button type="button" class="filtrar" onclick="filtrarPagos()">Filtrar</button>
                    <button type="button" class="limpiar-filtros" onclick="limpiarFiltrosPagos()"> Limpiar filtros </button>
                </div>
                <p id="mensajeFiltrosPagos"></p>
                <div class="tabla-pagos-contenedor">
                    <table class="tabla-pagos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Producto (Cant.)</th>
                                <th>Subtotal</th>
                                <th>Comprobante</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tablapagos">
                            <tr>
                                <td colspan="7" style="text-align: center;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tutoriales -->
            <div id="Tutoriales" class="formulario tarjeta tutoriales-cards">
                
            </div>
        </main>
    </div>
    <script src="panel.js"></script>
</body>
</html>