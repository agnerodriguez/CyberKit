<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detecta el nombre del archivo actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="nav.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class="nav-contenedor">
    <nav class="nav-barra">

        <button class="menu-hamburguesa" id="hamburguesa" onclick="menuHamburguesaMovil()">
            <span class="icono-abrir"><img src="iconos/menu.png" alt="Abrir"></span>
            <span class="icono-cerrar"><img src="iconos/cerrar.png" alt="Cerrar"></span>
        </button>

        <ul class="nav-lista" id="nav-lista">
            <li class="<?= $pagina_actual == 'inicio.php' ? 'activo' : '' ?>">
                <a href="inicio.php" onclick="Seleccionado(this)">Inicio</a>
            </li>

            <li class="<?= $pagina_actual == 'comprar.php' ? 'activo' : '' ?>">
                <a href="comprar.php" onclick="Seleccionado(this)">Comprar</a>
            </li>

            <li class="<?= $pagina_actual == 'tutoriales.php' ? 'activo' : '' ?>">
                <a href="tutoriales.php" onclick="Seleccionado(this)">Tutoriales</a>
            </li>

            <li class="<?= $pagina_actual == 'foro.php' ? 'activo' : '' ?>">
                <a href="foro.php" onclick="Seleccionado(this)">Nuestro foro</a>
            </li>

            <li class="<?= $pagina_actual == 'nosotros.php' ? 'activo' : '' ?>">
                <a href="nosotros.php" onclick="Seleccionado(this)">Nosotros</a>
            </li>

            <li class="<?= ($pagina_actual == 'cuenta.php') ? 'activo' : '' ?>">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="cuenta.php" onclick="Seleccionado(this)">Cuenta</a>
                <?php else: ?>
                    <a href="iniciarSesion.php">Cuenta</a>
                <?php endif; ?>  
            </li>

            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                <li class="<?= $pagina_actual == 'panel.php' ? 'activo' : '' ?>">
                    <a href="panel.php" onclick="Seleccionado(this)">Panel de administración</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<script src="nav.js"></script>