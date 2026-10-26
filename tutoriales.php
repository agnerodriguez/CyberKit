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
                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Aprende como funciona la Raspberry Pi', 'Descripción general de funcionamiento y componentes principales.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
                    <div class="encabezado-ventana-retro">
                        <span class="titulo-ventana-retro">Data</span>
                        <div class="puntos-ventana-retro">
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                        </div>
                    </div>
                    <div class="contenido-ventana-retro">
                        <div class="miniatura-video-tutorial">
                            <img src="https://images.unsplash.com/photo-1629654297299-c8506221ca97?auto=format&fit=crop&w=500&q=80" alt="Raspberry Pi">
                            <div class="boton-reproducir-circular">
                                <div class="triangulo-reproducir"></div>
                            </div>
                        </div>
                        <p class="titulo-video-tutorial">Aprende como funciona la Raspberry Pi</p>
                    </div>
                </div>

                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Arma tu Cyberdesk paso a paso', 'Guía de ensamblaje paso a paso del gabinete y componentes.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
                    <div class="encabezado-ventana-retro">
                        <span class="titulo-ventana-retro">Data</span>
                        <div class="puntos-ventana-retro">
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                        </div>
                    </div>
                    <div class="contenido-ventana-retro">
                        <div class="miniatura-video-tutorial">
                            <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=500&q=80" alt="Cyberdesk Paso a paso">
                            <div class="boton-reproducir-circular">
                                <div class="triangulo-reproducir"></div>
                            </div>
                        </div>
                        <p class="titulo-video-tutorial">Arma tu Cyberdesk paso a paso</p>
                    </div>
                </div>

                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Configuración del sistema', 'Tutorial de configuración y ajustes iniciales de software.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
                    <div class="encabezado-ventana-retro">
                        <span class="titulo-ventana-retro">Data</span>
                        <div class="puntos-ventana-retro">
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                        </div>
                    </div>
                    <div class="contenido-ventana-retro">
                        <div class="miniatura-video-tutorial">
                            <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=500&q=80" alt="Tutorial">
                            <div class="boton-reproducir-circular">
                                <div class="triangulo-reproducir"></div>
                            </div>
                        </div>
                        <p class="titulo-video-tutorial">Configuración de software</p>
                    </div>
                </div>

                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Configuración del sistema', 'Tutorial de configuración y ajustes iniciales de software.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
                    <div class="encabezado-ventana-retro">
                        <span class="titulo-ventana-retro">Data</span>
                        <div class="puntos-ventana-retro">
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                            <span class="punto-ventana-retro"></span>
                        </div>
                    </div>
                    <div class="contenido-ventana-retro">
                        <div class="miniatura-video-tutorial">
                            <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=500&q=80" alt="Tutorial">
                            <div class="boton-reproducir-circular">
                                <div class="triangulo-reproducir"></div>
                            </div>
                        </div>
                        <p class="titulo-video-tutorial">Configuración de software</p>
                    </div>
                </div>
            </div>

            <div class="barra-progreso-videos">
                <div class="progreso-videos"></div>
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
                        <div class="columna-pdf">
                            <div class="tarjeta-pdf1">
                                <h3>MANUAL DE ARMADO</h3>
                                <p>Guía paso a paso</p>
                                <p>Lista de materiales</p>
                                <p>Diagramas</p>
                            </div>
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                        </div>

                        <div class="columna-pdf columna-pdf-desplazada">
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                            <div class="tarjeta-pdf2">
                                <h3>CABLEADO</h3>
                                <p>Esquemas</p>
                                <p>Voltajes</p>
                                <p>Recomendaciones</p>
                            </div>
                        </div>

                        <div class="columna-pdf">
                            <div class="tarjeta-pdf3">
                                <h3>SOFTWARE</h3>
                                <p>Sistemas compatibles</p>
                                <p>Instalación</p>
                                <p>Ajustes</p>
                            </div>
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                        </div>

                        <div class="columna-pdf columna-pdf-desplazada">
                            <button class="boton-descargar-pdf">DESCARGAR</button>
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