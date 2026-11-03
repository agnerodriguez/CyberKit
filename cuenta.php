<?php
session_start();

// Control de acceso: Verificar si el usuario está autenticado
if (empty($_SESSION['id_usuario'])) {
    header("Location: inicioSesion.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="cuenta.css">
    <!-- Fuente retro estilo pixel / terminal -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Iconos de Bootstrap para UI limpia -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body data-userid="<?php echo htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8'); ?>">
    <?php include 'nav.php'; ?>

    <main class="contenedor-general">
        <h1 class="titulo-principal">Mi Cuenta</h1><!-- Título Superior -->

        <div class="contenedor-principal">
            <!-- BARRA LATERAL IZQUIERDA CON BORDE VERDE NEÓN -->
            <aside class="tarjeta-barra-lateral">
                <div class="cabecera-usuario">
                    <div class="contenedor-avatar">
                        <div class="circulo-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <button class="boton-editar-avatar" title="Cambiar foto">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                    </div>
                    <span id="sidebar-username" class="texto-nombre-usuario">Cyberuser</span>
                </div>

                <nav class="menu-lateral">
                    <button class="boton-navegacion activo" data-target="seccion-perfil">
                        <span>Mi perfil</span>
                        <div class="circulo-flecha"><i class="bi bi-chevron-down"></i></div>
                    </button>

                    <button class="boton-navegacion" data-target="seccion-compras">
                        <span>Mis Compras</span>
                        <div class="circulo-flecha"><i class="bi bi-chevron-right"></i></div>
                    </button>

                    <form action="inicio.php" method="POST" class="formulario-salir">
                        <button type="submit" class="boton-navegacion salir">
                            <span>Cerrar Sesion</span>
                            <div class="circulo-flecha"><i class="bi bi-arrow-counterclockwise"></i></div>
                        </button>
                    </form>
                </nav>
            </aside>

            <!-- CONTENIDO DERECHO (PANELES DINÁMICOS) -->
            <section class="area-contenido">

                <!-- MODAL / VISTA 1: INFORMACIÓN DE PERFIL -->
                <div id="seccion-perfil" class="vista-cyber activo">
                    <div class="tarjeta-panel">
                        <h2 class="titulo-vista">Información de Perfil</h2>

                        <form id="form-perfil" class="rejilla-perfil">
                            <!-- Fila 1: Nombre & Email -->
                            <div class="grupo-formulario">
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre" readonly required maxlength="50" class="campo-entrada-pill">
                            </div>

                            <div class="grupo-formulario">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" id="email" readonly required maxlength="255" class="campo-entrada-pill">
                            </div>

                            <!-- Fila 2: Edad & Contraseña -->
                            <div class="grupo-formulario">
                                <label for="edad">Edad</label>
                                <input type="number" id="edad" min="1" max="120" readonly required class="campo-entrada-pill">
                            </div>

                            <div class="grupo-formulario">
                                <label for="password">Contraseña</label>
                                <input type="password" id="password" readonly autocomplete="new-password" placeholder="Dejar en blanco para mantenerla" class="campo-entrada-pill">
                            </div>

                            <!-- Botón Acción abajo a la derecha -->
                            <div class="pie-formulario ancho-completo">
                                <button type="button" id="btn-editar" class="boton-pastilla-rosa">
                                    Editar Perfil <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="submit" id="btn-guardar" class="boton-pastilla-rosa brillo-verde" style="display: none;">
                                    Guardar Cambios <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL / VISTA 2: MIS COMPRAS -->
                <div id="seccion-compras" class="vista-cyber">
                    
                    <!-- BLOQUE 1: MIS COMPRAS -->
                    <div class="tarjeta-panel margen-inferior">
                        <h2 class="titulo-vista">Mis Compras</h2>

                        <div class="lista-compras" id="lista-compras">
                            <!-- Inyectado dinámicamente vía Fetch API -->
                            <div class="estado-carga">Cargando compras desde la red...</div>
                        </div>
                    </div>

                </div>

            </section>
        </div>
    </main>

    <script src="cuenta.js"></script>
</body>
</html>