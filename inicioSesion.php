<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include('conexion.php');

    $mensajeError = ""; 
    $scriptConsola = "";

    function consoleError($mensaje) {
        $jsMensaje = json_encode("Error de la API: " . $mensaje);
        return "<script>console.error($jsMensaje);</script>";
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = trim($_POST["email"] ?? '');
        $contrasena = $_POST["contrasena"] ?? '';
        $recurso = $_POST["recurso"] ?? 'Usuarios';
        $consulta = $_POST["consulta"] ?? 'IniciarSesion';

        $datos = [
            "email" => $email,
            "contrasena" => $contrasena,
            "recurso" => $recurso,
            "consulta" => $consulta
        ];

        $payload = json_encode($datos);

        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $rutaLimpia = implode('/', array_map('rawurlencode', explode('/', dirname($_SERVER['PHP_SELF']))));
        $urlApi = $protocolo . $host . $rutaLimpia . "/API.php";

        $opciones = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content" => $payload,
                "ignore_errors" => true // Capturar la respuesta JSON incluso si hay un código HTTP de error (401, 404, etc.)
            ]
        ];

        $contexto = stream_context_create($opciones);
        $resultado = @file_get_contents($urlApi, false, $contexto);

        if ($resultado !== false) {
            $respuesta = json_decode($resultado, true);

            if (isset($respuesta['idUsuario'])) {
                $_SESSION['id_usuario'] = $respuesta['idUsuario'];
                $_SESSION['usuario'] = $respuesta['usuario'];
                $_SESSION['admin'] = $respuesta['admin'] ?? 0;
                header('Location: inicio.php');
                exit;
            } else {
                $mensajeError = $respuesta['error'] ?? "Error desconocido en la respuesta.";
                $scriptConsola = consoleError($mensajeError); 
            }
        } else {
            $mensajeError = "No se pudo realizar la llamada HTTP a la API.";
            $scriptConsola = consoleError("Error de red al conectar a " . $urlApi); 
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicia Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="inicioSesion.css">
</head>
<body>
    <div class="lineas-crt"></div>

    <div class="contenedor-principal">
        <header class="encabezado">
            <h1 class="titulo-principal">INICIÁ SESIÓN</h1>
            <p class="subtitulo">Iniciá sesión para navegar en el sitio.</p>
        </header>

        <div class="marco-exterior">
            <div class="tarjeta-login">
                <?php if (!empty($mensajeError)): ?>
                    <p style="color: red; text-align: center; font-weight: bold; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($mensajeError); ?>
                    </p>
                <?php endif; ?>

                <form id="login-form" method="POST">
                    
                    <div class="campo">
                        <div class="caja-etiqueta">
                            <img src="iconos/avatar.png" alt="Usuario" class="icono">
                            <label for="login-usuario" class="etiqueta">email</label>
                        </div>
                        <div class="caja-entrada">
                            <input type="email" id="login-usuario" name="email" class="entrada" placeholder="escribe aqui...." required>
                        </div>
                    </div>

                    <div class="campo">
                        <div class="caja-etiqueta">
                            <img src="iconos/desbloquear.png" alt="Candado" class="icono">
                            <label for="login-password" class="etiqueta">contraseña</label>
                        </div>
                        <div class="caja-entrada campo-password">
                            <input type="password" id="login-password" name="contrasena" class="entrada" placeholder="escribe aqui...." required>
                            <button type="button" class="boton-ojito" id="login-boton-ojo">
                                <span class="icono-ojito"></span>
                            </button>
                        </div>
                        <p class="linkOlvido"><a href="#" id="btnOlvido">Olvidé mi contraseña</a></p>
                        <?php if (isset($mensajeError)): ?>
                            <p class="error"><?= $mensajeError ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bloque-acciones">
                        <button type="submit" class="boton-ingresar">Iniciar sesión</button>
                        <p class="texto-registro">
                            ¿No tienes cuenta? <a href="registrarse.php" class="enlace-crear">¡Registrate!</a>
                        </p>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div id="modalOlvido" class="modal-olvido" style="display: none;">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="cerrarModal">&times;</span>
            <h3 style="text-align: center; margin-bottom: 10px; font-family: 'Tamrin'; font-size: 18px;">Recuperar Contraseña</h3>
            <p style="text-align: center; font-size: 12px; margin-bottom: 15px; color: black; font-family: 'Tamrin';">
                Ingrese el email. Se restablecerá la contraseña y se enviarán los datos a su correo electrónico.
            </p>
            
            <form id="formOlvido">
                <div class="grupoInput">
                    <label for="contiOlvido" class="label">Email</label>
                    <input type="text" id="contOlvido" name="email" required placeholder="Ingrese su email" class="input">
                </div>
                <button type="submit" class="btn" style="margin-top: 15px;">Enviar correo</button>
            </form>
            
            <p id="mensajeModal"></p>
        </div>
    </div>

    <?php echo $scriptConsola; ?> 
    <script src="inicioSesion.js"></script>
</body>
</html>