<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="footer.css">
</head>
<body>
    <footer class="contenedor">
        <div class="columna-izquierda">
            <div class="logo">
                <img src="iconos/logoEmpresa.png" alt="Logo">
            </div>

            <div class="reconocimiento">
                <p class="fuenteSecundaria">
                    Este sitio fue desarrollado por estudiantes de séptimo año de la Tecnicatura en Programación: 
                    Alejandra Gomez, Daira Obando, Agnetha Rodriguez, Pilar Laborde.
                </p>
            </div>

            <div class="redes">
                <div class="fondoIg">
                    <div class="fondoSuperpuestoIg">
                        
                        <a href="https://www.instagram.com/recy.techh/?utm_source=ig_web_button_share_sheet"><img src="iconos/ig.png" alt="IG"></a>
                    </div>
                </div>
                <span class="fuenteSecundaria"><a href="https://www.instagram.com/recy.techh/?utm_source=ig_web_button_share_sheet">recy.techh</a></span>

                <div class="fondoMail">
                    <div class="fondoSuperpuestoMail">
                        <img src="iconos/gmail.png" alt="Mail">
                    </div>
                </div>
                <span class="fuenteSecundaria" style="cursor: pointer;" id="mail" onclick="copiarElemento('mail')">recytech.support@gmail.com</span>
                <p id="mensajeCopiar" class="fuenteSecundaria"></p>
            </div>
        </div>

        <div class="navegacion">
            <h2 class="fuentePrincipal">Navegación</h2>
            <li class="fuenteSecundaria"><a href="inicio.php">Inicio</a></li>
            <li class="fuenteSecundaria"><a href="comprar.php">Comprar</a></li>
            <li class="fuenteSecundaria"><a href="tutoriales.php">Tutoriales</a></li>
            <li class="fuenteSecundaria"><a href="foro.php">Nuestro foro</a></li>
        </div>
    </footer>

    <script src="footer.js"></script>

</body>
</html>