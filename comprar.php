<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="comprar.css">
    <title>Comprar</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="contenedor activo" id="principal">

        <div class="columnaIzquierda">
            <div class="fondoProductoRosa">
                <div class="fondoProductoVioleta">
                    <div id="imagen" class="imagenProducto"><img src="productos/CyberKit.png" alt="Cyberkit"></div>
                </div>
            </div>
        </div>

        <div class="infoProducto">
            <h1 id="nombreProducto" class="fuentePrincipal">...</h1>
            <h2 id="precio" class="fuentePrincipal">$$$$</h2>
            <h2 class="fuentePrincipal cantidad">Cantidad</h2>
            <div class="contador">
                <button class="btnMenos fuentePrincipal" id="menos">-</button>
                <span class="valor fuentePrincipal" id="valor">0</span>
                <button class="btnMas fuentePrincipal" id="mas">+</button>
            </div>
            <p id="Descripcion" class="fuenteSecundaria">...</p>
        </div>

        <div class="contenedorBtn fuentePrincipal">
            <button type="button" class="btnEnviar fuentePrincipal" onclick="mostrar('enviarComprobante')">Comprar</button>
        </div>
    </div>

    <div class="contenedor" id="enviarComprobante">
        <div class="fondoRosa">
            <div class="fondoVioleta">
                <h1 class="fuentePrincipal">Detalles del pago</h1>
                
                <div class="contenidoPago">
                    <!-- Columna Izquierda: Cargar -->
                    <div class="cargar">
                        <label for="subir" class="cargarImagen">
                            <img src="iconos/cargar.png" alt="Cargar">
                            <span class="textoCargar fuenteSecundaria" id="textoSubir">SUBIR FOTO DEL COMPROBANTE</span>
                        </label>
                    </div>

                    <!-- Columna Derecha: Información -->
                    <div class="info">
                        <h2 class="fuentePrincipal">ALIAS</h2>
                        <p class="fuentePrincipal" onclick="copiarElemento('alias')">$$$$$</p>

                        <h2 class="fuentePrincipal">CBU</h2>
                        <p class="fuentePrincipal" onclick="copiarElemento('cbu')">$$$$$</p>
                    </div>
                </div>

                <button type="button" class="btnComprobante fuentePrincipal" onclick="enviarPagoAPI()">Pagar</button>
                
                <p id="mensajeComprobante" style="display:none; text-align:center; font-family:Tamrin; font-size:12px; color: yellow; margin-top: 10px"></p>
            </div>
        </div>
    </div>
    <script src="comprar.js"></script>
</body>
</html> 