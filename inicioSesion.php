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
            "mensaje" => "Usuario recibido correctamente",
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
                "content"  => $payload,
                "ignore_errors" => true
            ]
        ];

        $contexto = stream_context_create($opciones);
        $resultado = @file_get_contents($urlApi, false, $contexto);

        if ($resultado !== false) {
            $respuesta = json_decode($resultado, true);

            if (isset($respuesta['mensaje'])) {
                $_SESSION['id'] = $respuesta['id_usuario'];
                $_SESSION['usuario'] = $respuesta['usuario'];
                $_SESSION['admin'] = $respuesta['admin'] ?? 0;
                header('Location: inicio.php');
                exit;
            } else {
                $mensajeError = $respuesta['error'];

                $errorDetalle = $respuesta['error'] ?? "Error desconocido de la API." . htmlspecialchars($resultado);
                $scriptConsola = consoleError($errorDetalle); 
            }
        } else {
            $mensajeError = "No se pudo realizar la llamada HTTP a la API.";

            $errorDetalle = "Error de red o file_get_contents falló al conectar a: " . $urlApi;
            $scriptConsola = consoleError($errorDetalle); 
        }
    }
?>

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
                <form id="login-form" method="POST">
                    
                    <div class="campo">
                        <div class="caja-etiqueta">
                            <img src="iconos/avatar.png" alt="Usuario" class="icono">
                            <label for="login-usuario" class="etiqueta">nombre de usuario o email</label>
                        </div>
                        <div class="caja-entrada">
                            <input type="text" id="login-usuario" name="email" class="entrada" placeholder="escribe aqui...." required>
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