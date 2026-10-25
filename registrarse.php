<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    $mensajeError = "";
    $scriptConsola = "";

    function consoleError($mensaje) {
        $jsMensaje = json_encode("Error de la API: " . $mensaje);
        return "<script>console.error($jsMensaje);</script>";
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST["nombre"] ?? '');
        $email  = trim($_POST["email"] ?? '');
        $edad   = trim($_POST["edad"] ?? '');
        $contrasena = $_POST["contrasena"] ?? '';

        $datos = [
            "mensaje"    => "Usuario recibido correctamente",
            "nombre"     => $nombre,
            "email"      => $email,
            "edad"       => $edad,
            "contrasena" => $contrasena,
            "recurso"    => "Usuarios",
            "consulta"   => "Create"
        ];

        $payload = json_encode($datos);

        // Construir la URL codificando espacios y caracteres especiales
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $rutaLimpia = implode('/', array_map('rawurlencode', explode('/', dirname($_SERVER['PHP_SELF']))));
        $urlApi = $protocolo . $host . $rutaLimpia . "/API.php";

        $opciones = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content" => $payload,
                "ignore_errors" => true
            ]
        ];

        $contexto = stream_context_create($opciones);
        $resultado = @file_get_contents($urlApi, false, $contexto);

        if ($resultado !== false) {
            $respuesta = json_decode($resultado, true);

            if (!empty($respuesta['error'])) {
                $mensajeError = $respuesta['error'];
                $scriptConsola = consoleError($mensajeError);
            } elseif (isset($respuesta['idUsuario'])) {
                $_SESSION['id_usuario'] = $respuesta['idUsuario'];
                $_SESSION['usuario'] = $respuesta['usuario'] ?? $nombre;
                $_SESSION['admin'] = $respuesta['admin'] ?? 0;
                header("Location: inicioSesion.php");
                exit;
            } else {
                $mensajeError = "Respuesta inesperada de la API.";
                $scriptConsola = consoleError(htmlspecialchars($resultado));
            }
        } else {
            $mensajeError = "No se pudo realizar la llamada HTTP a la API.";
            $scriptConsola = consoleError("Error de red o file_get_contents falló al conectar a: " . $urlApi);
        }
    }
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="registrarse.css">
</head>
<body>
    <div class="cyber-doble">
        
        <div class="cyber-header">
            <h1 class="titulo">REGISTRARSE</h1>
            <p class="subtitulo">Creá una cuenta para navegar en el sitio.</p>
        </div>

        <div class="card-contenedor">
            
            <div class="pink-layer"></div>
            <div class="blue-layer"></div>

            <div class="cyber-card">
                <?php if (!empty($mensajeError)): ?>
                    <p style="color: red; text-align: center; font-weight: bold; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($mensajeError); ?>
                    </p>
                <?php endif; ?>
                
                <form action="registrarse.php" method="POST" id="registerForm" class="cyber-form">
                    
                    <div class="form-grid">
                        
                        <!--  Nombre -->
                        <div class="inputs">
                            <label for="nombre">
                                <div class="iconousuario"></div>
                                Ingrese su nombre
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="nombre" name="nombre" placeholder="escribe aqui....">
                            </div>
                        </div>

                        <!--  Email -->
                        <div class="inputs">
                            <label for="email">
                                <div class="iconomail"></div>
                                Ingrese su email
                            </label>
                            <div class="input-wrapper">
                                <input type="email" id="email" name="email" placeholder="escribe aqui....">
                            </div>
                        </div>

                        <!--  Edad -->
                        <div class="inputs">
                            <label for="edad">
                                <div class="icono-edad"></div>
                                Ingrese su edad
                            </label>
                            <div class="input-wrapper">
                                <input type="number" id="edad" name="edad" placeholder="escribe aqui....">
                            </div>
                        </div>

                        <!--  Contraseña -->
                        <div class="inputs">
                            <label for="password">
                                <div class="iconocontrasena"></div>
                                Crea una contraseña
                            </label>
                            <div class="input-wrapper password-wrapper">
                                <input type="password" id="password" name="contrasena" placeholder="escribe aqui....">
                                <button type="button" class="toggle-password" id="togglePassword">
                                    <div class="icono-ojito"></div>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="cyber-btn">Registrarse</button>
                        <a href="inicioSesion.php" class="cyber-link">
                            <span class="link-soft">¿Ya tienes cuenta? </span><span class="link-bright">¡Iniciá sesión!</span>
                        </a>
                    </div>

                </form>
            </div>

        </div>

    </div>

    <script src="registrarse.js"></script>
</body>
</html>