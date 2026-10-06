<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titulo</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="registrarse.css">
</head>
<body>
    <div class="cyber-doble">
        
        <div class="cyber-header">
            <h1 class="titulo">REGISTRARSE</h1>
            <p class="subtitulo">crea una cuenta para navegar el sitio</p>
        </div>

        <div class="card-contenedor">
            
            <div class="pink-layer"></div>
            <div class="blue-layer"></div>

            <div class="cyber-card">
                
                <form action="registro.php" method="POST" id="registerForm" class="cyber-form">
                    
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
                                <input type="password" id="password" name="password" placeholder="escribe aqui....">
                                <button type="button" class="toggle-password" id="togglePassword">
                                    <div class="icono-ojito"></div>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="cyber-btn">Registrarse</button>
                        <a href="login.php" class="cyber-link">
                            <span class="link-soft">¿Ya tienes cuenta? </span><span class="link-bright">Inicia sesión</span>
                        </a>
                    </div>

                </form>
            </div>

        </div>

    </div>

    <script src="registrarse.js"></script>
</body>
</html>