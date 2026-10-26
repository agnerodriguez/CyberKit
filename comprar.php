<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include('conexion.php');

    // Validar si el usuario no ha iniciado sesión o se ha registrado
    if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
        header('Location: inicioSesion.php');
        exit;
    }

    $idUsuario = $_SESSION['id_usuario'] ?? 0;

    /*$idProducto = (int)($_POST['id_producto'] ?? 0); */
    
?>
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

    <div class="contenido">
        <div class="contenedor activo" id="principal">

            <p id="mensajeErrorMostrarProducto" class="fuenteSecundaria" style="display:none; grid-column: 1 / -1; width: 100%; text-align: center; font-size: 0.85rem; color: #ff0055; background-color: rgba(255, 0, 85, 0.15); border: 2px solid #ff0055; box-shadow: 0 0 10px #ff0055; border-radius: 6px; padding: 12px; margin-bottom: 10px; text-shadow: 0 0 4px #ff0055;"></p>

            <div class="columnaIzquierda">
                <div class="fondoProductoRosa">
                    <div class="fondoProductoVioleta">
                        <div id="imagen" class="imagenProducto"><img src="productos/CyberKit.png" alt=""></div>
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

                <h2 id="subtotalDinamico" class="fuentePrincipal cantidad" style="color: #00ffff; margin-top: 5px;">Subtotal: $0.00</h2>
                
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

                            <input type="file" id="subir" name="comprobante" accept="image/*" style="display: none;" onchange="comprobanteSeleccionado()">
                        </div>

                        <!-- Columna Derecha: Información -->
                        <div class="info">
                            <h2 class="fuentePrincipal">ALIAS</h2>
                            <p class="fuentePrincipal" id="alias" onclick="copiarElemento('alias')">$$$$$</p>

                            <h2 class="fuentePrincipal">CBU</h2>
                            <p class="fuentePrincipal" id="cbu" onclick="copiarElemento('cbu')">$$$$$</p>

                            <p id="mensajeCopiar" class="fuenteSecundaria"></p>
                        </div>
                    </div>

                    <input type="hidden" id="idProducto" value="<?php echo htmlspecialchars($idProducto)?>">
                    <input type="hidden" id="idUsuario" value="<?php echo htmlspecialchars($idUsuario)?>">

                    <button type="button" class="btnComprobante fuentePrincipal" onclick="enviarPagoAPI()">Pagar</button>
                    
                    <p id="mensajeComprobante"  class="fuenteSecundaria" style="display:none; text-align:center; font-size:12px; color: yellow; margin-top: 10px"></p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="comprar.js"></script>
</body>
</html> 