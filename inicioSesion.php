<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicia Sesión - Cyberkit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="inicioSesion.css">
</head>
<body>
    <div class="lineas-crt"></div>

    <main class="contenedor-principal">
        <header class="encabezado">
            <h1 class="titulo-principal">INICIA SESIÓN</h1>
            <p class="subtitulo">inicia sesión para accder al cyberkit</p>
        </header>

        <div class="marco-exterior">
            <div class="tarjeta-login">
                <form id="login-form" action="login_process.php" method="POST">
                    
                    <div class="campo">
                        <div class="caja-etiqueta">
                            <img src="iconos/avatar.png" alt="Usuario" class="icono">
                            <label for="login-usuario" class="etiqueta">nombre de usuario o email</label>
                        </div>
                        <div class="caja-entrada">
                            <input type="text" id="login-usuario" name="usuario_email" class="entrada" placeholder="escribe aqui...." required>
                        </div>
                    </div>

                    <div class="campo">
                        <div class="caja-etiqueta">
                            <img src="iconos/desbloquear.png" alt="Candado" class="icono">
                            <label for="login-password" class="etiqueta">contraseña</label>
                        </div>
                        <div class="caja-entrada campo-password">
                            <input type="password" id="login-password" name="password" class="entrada" placeholder="escribe aqui...." required>
                            <button type="button" class="boton-ojito" id="login-boton-ojo">
                                <span class="icono-ojito"></span>
                            </button>
                        </div>
                    </div>

                    <div class="bloque-acciones">
                        <button type="submit" class="boton-ingresar">Iniciar sesión</button>
                        <p class="texto-registro">
                            ¿No tienes cuenta? <a href="registrarse.php" class="enlace-crear">Crea una</a>
                        </p>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script src="inicioSesion.js"></script>
</body>
</html>