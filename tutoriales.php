<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberdesk - Tutoriales</title>
    
    <!-- Carga de fuentes retro y pixeladas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="tutoriales.css">
</head>
<body>

    <div class="lineas-pantalla-crt"></div>

    <!-- Inclusión de la navegación principal -->
    <?php include 'nav.php'; ?>

    <main class="contenedor-contenido">

        <!-- VISTA 1: Menú principal -->
        <section id="seccion-menu-tutoriales" class="seccion-vista activo">
            <h1 class="titulo-pixel brillo-rosa">TUTORIALES</h1>
            <p class="texto-subtitulo">Encontrá los mejores tutoriales y manuales para armar tus Cyberdesk y solucionar problemas</p>
            <p class="etiqueta-seccion">ELIGE EL TIPO DE TUTORIAL</p>

            <div class="contenedor-tarjetas-menu">
                <div class="tarjeta-menu-video" onclick="navigateTo('seccion-videos-tutoriales')">
                    <div class="vista-previa-tarjeta">
                        <img src="img/tutovideos.png" alt="Video Preview Cyberdesk" class="imagen-previa-tarjeta">
                        <div class="capa-icono-tarjeta">
                            <div class="boton-reproducir-circular">
                                <div class="triangulo-reproducir"></div>
                            </div>
                        </div>
                    </div>
                    <div class="contenido-tarjeta-menu">
                        <h2>TUTORIALES EN VIDEO</h2>
                        <p>Aprende paso a paso con nuestros videos tutoriales desde el armado hasta ideas de personalizacion</p>
                        <button class="boton-retro-menu">VER VIDEOS <span class="flecha-boton">➔</span></button>
                    </div>
                </div>

                <div class="tarjeta-menu-pdf" onclick="navigateTo('seccion-manuales-pdf')">
                    <div class="vista-previa-tarjeta">
                        <img src="img/tutopdf.png" alt="PDF Preview Cyberdesk" class="imagen-previa-tarjeta">
                        <div class="capa-icono-tarjeta">
                            <div class="icono-pdf-circular">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#d946ef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="contenido-tarjeta-menu">
                        <h2>TUTORIALES EN PDF</h2>
                        <p>Descarga manuales detallados en PDF con instrucciones, guias y consejos practicos</p>
                        <button class="boton-retro-menu">VER PDF <span class="flecha-boton">➔</span></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- VISTA 2: Galería de videos -->
        <section id="seccion-videos-tutoriales" class="seccion-vista">
            <h1 class="titulo-pixel brillo-rosa">VIDEOS TUTORIALES</h1>
            <p class="texto-subtitulo">Aprende paso a paso con nuestros videos tutoriales desde el armado hasta ideas de personalizacion</p>

            <div class="rejilla-videos-tutoriales">
                <?php
                // Incluimos la conexión si no está cargada arriba
                include_once("conexion.php");

                $sql = "SELECT id_tutorial, nombre, descripcion, archivo FROM tutoriales ORDER BY id_tutorial DESC";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        $id = (int)$fila['id_tutorial'];
                        $nombreHtml = htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8');
                        $archivo = trim($fila['archivo']);

                        // Extraer la miniatura de YouTube automáticamente
                        $miniatura = "img/tutovideos.png";
                        if (preg_match('/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?v=|embed\/))([A-Za-z0-9_-]{11})/', $archivo, $match)) {
                            $miniatura = "https://img.youtube.com/vi/" . $match[1] . "/hqdefault.jpg";
                        }
                ?>
                        <!-- Ventana retro generada por cada fila de la BD -->
                        <div class="ventana-retro-video video-tutorial" onclick="openVideoModal(<?php echo $id; ?>)">
                            <div class="encabezado-ventana-retro">
                                <span class="titulo-ventana-retro">Tutorial #<?php echo $id; ?></span>
                                <div class="puntos-ventana-retro">
                                    <span class="punto-ventana-retro"></span>
                                    <span class="punto-ventana-retro"></span>
                                    <span class="punto-ventana-retro"></span>
                                </div>
                            </div>
                            <div class="contenido-ventana-retro">
                                <div class="miniatura-video-tutorial">
                                    <img src="<?php echo $miniatura; ?>" alt="<?php echo $nombreHtml; ?>">
                                    <div class="boton-reproducir-circular">
                                        <div class="triangulo-reproducir"></div>
                                    </div>
                                </div>
                                <p class="titulo-video-tutorial"><?php echo $nombreHtml; ?></p>
                            </div>
                        </div>
                <?php 
                    }
                } else {
                    echo "<p style='color: #f3e8ff; grid-column: 1/-1; text-align: center;'>No hay tutoriales disponibles en la base de datos.</p>";
                }
                ?>
            </div>
        </section>

       <!-- VISTA 3: Galería PDF -->
        <section id="seccion-manuales-pdf" class="seccion-vista">
            <h1 class="titulo-pixel brillo-rosa">TUTORIALES EN PDF</h1>

            <div class="ventana-principal-pdfs">
                <div class="encabezado-ventana-retro">
                    <span class="titulo-ventana-pdf">Encontrá el manual adecuado</span>
                    <div class="puntos-ventana-retro">
                        <span class="punto-ventana-retro"></span>
                        <span class="punto-ventana-retro"></span>
                        <span class="punto-ventana-retro"></span>
                    </div>
                </div>

                <div class="contenido-ventana-pdfs">
                    <div class="rejilla-columnas-pdf">
                        
                        <!-- PDF 1: Manual de Armado -->
                        <div class="columna-pdf">
                            <div class="tarjeta-pdf1">
                                <h3>MANUAL DE ARMADO</h3>
                                <p>Guía paso a paso</p>
                                <p>Lista de materiales</p>
                                <p>Diagramas</p>
                            </div>
                            <a href="pdf/armado.pdf" download="armado.pdf" style="text-decoration: none;">
                                <button class="boton-descargar-pdf" style="width: 100%;">DESCARGAR</button>
                            </a>
                        </div>

                        <!-- PDF 2: Cableado -->
                        <div class="columna-pdf columna-pdf-desplazada">
                            <a href="pdf/cableado.pdf" download="cableado.pdf" style="text-decoration: none;">
                                <button class="boton-descargar-pdf" style="width: 100%;">DESCARGAR</button>
                            </a>
                            <div class="tarjeta-pdf2">
                                <h3>CABLEADO</h3>
                                <p>Esquemas</p>
                                <p>Voltajes</p>
                                <p>Recomendaciones</p>
                            </div>
                        </div>

                        <!-- PDF 3: Software -->
                        <div class="columna-pdf">
                            <div class="tarjeta-pdf3">
                                <h3>SOFTWARE</h3>
                                <p>Sistemas compatibles</p>
                                <p>Instalación</p>
                                <p>Ajustes</p>
                            </div>
                            <a href="pdf/software.pdf" download="software.pdf" style="text-decoration: none;">
                                <button class="boton-descargar-pdf" style="width: 100%;">DESCARGAR</button>
                            </a>
                        </div>

                        <!-- PDF 4: Resolución -->
                        <div class="columna-pdf columna-pdf-desplazada">
                            <a href="pdf/resolucion.pdf" download="sresolucion.pdf" style="text-decoration: none;">
                                <button class="boton-descargar-pdf" style="width: 100%;">DESCARGAR</button>
                            </a>
                            <div class="tarjeta-pdf4">
                                <h3>RESOLUCIÓN</h3>
                                <p>Problemas comunes</p>
                                <p>Soluciones</p>
                                <p>Contacto</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Modal emergente para reproducción de video -->
    <div id="modal-reproduccion-video" class="fondo-modal-video">
        <div class="contenedor-modal-video">
            <button class="boton-cerrar-modal-video" onclick="closeVideoModal()">✕</button>
            <div class="rejilla-contenido-modal-video">
                <div class="ventana-retro-video ventana-reproductor-video">
                    <div class="encabezado-ventana-retro">
                        <span class="titulo-ventana-retro">Vídeo</span>
                        <div class="puntos-ventana-retro">
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                        </div>
                    </div>
                    <div class="contenido-ventana-retro contenido-reproductor-video">
                        <iframe id="reproductor-iframe-modal" src="" title="Video Player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>

                <div class="panel-descripcion-video">
                    <h2 id="titulo-modal-video" class="titulo-brillo-cian">Aprende como funciona la Raspberry Pi</h2>
                    <div class="texto-descripcion-video">
                        <p id="descripcion-modal-video">Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="tutoriales.js"></script>
</body>
</html>