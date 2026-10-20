<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberdesk - Tutoriales</title>
    
    <!-- Carga las fuentes pixeladas y retro utilizadas por la interfaz. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Conecta los estilos visuales de esta página. -->
    <link rel="stylesheet" href="tutorial.css">
</head>
<body>

    <!-- Capa decorativa que simula las líneas de un monitor CRT. -->
    <div class="lineas-pantalla-crt"></div>

    <!-- Navegación principal; el enlace Tutoriales vuelve a la primera vista. -->
    <header class="encabezado-principal">
        <nav class="contenedor-navegacion">
            <a href="#" class="enlace-navegacion">Inicio</a>
            <a href="#" class="enlace-navegacion">Comprar</a>
            <a href="#" class="enlace-navegacion activo" id="enlace-nav-tutoriales">Tutoriales</a>
            <a href="#" class="enlace-navegacion">Nuestro foro</a>
            <a href="#" class="enlace-navegacion">Nosotros</a>
            <a href="#" class="enlace-navegacion">Cuenta</a>
        </nav>
    </header>

    <!-- Agrupa las vistas que JavaScript alterna sin recargar la página. -->
    <main class="contenedor-contenido">

        <!-- ========================================================= -->
        <!-- VISTA 1: menú para elegir entre tutoriales en video y manuales PDF. -->
        <!-- ========================================================= -->
        <section id="seccion-menu-tutoriales" class="seccion-vista activo">
            <h1 class="titulo-pixel brillo-rosa">TUTORIALES</h1>
            <p class="texto-subtitulo">Encontrá los mejores tutoriales y manuales para armar tus Cyberdesk y solucionar problemas</p>
            <p class="etiqueta-seccion">ELIGE EL TIPO DE TUTORIAL</p>

            <div class="contenedor-tarjetas-menu">
                <!-- Tarjeta 1: Video Tutoriales -->
                <div class="tarjeta-menu-video" onclick="navigateTo('seccion-videos-tutoriales')">
                    <div class="vista-previa-tarjeta">
                        <img src="img/tutovideo.png" alt="Video Preview Cyberdesk" class="imagen-previa-tarjeta">
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

                <!-- Tarjeta 2: PDF Tutoriales -->
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
                        <p>Descarga manuales detallados en PDF con instruccione, guias y consejos practicos</p>
                        <button class="boton-retro-menu">VER PDF <span class="flecha-boton">➔</span></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================= -->
        <!-- VISTA 2: galería; cada tarjeta abre el reproductor con su descripción. -->
        <!-- ========================================================= -->
        <section id="seccion-videos-tutoriales" class="seccion-vista">
            <h1 class="titulo-pixel brillo-rosa">VIDEOS TUTORIALES</h1>
            <p class="texto-subtitulo">Aprende paso a paso con nuestros videos tutoriales desde el armado hasta ideas de personalizacion</p>

            <div class="rejilla-videos-tutoriales">
                <!-- Video Item 1 -->
                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Aprende como funciona la Rassberry pi', 'DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
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
                        <p class="titulo-video-tutorial">Aprende como funciona la Rassberry pi</p>
                    </div>
                </div>

                <!-- Video Item 2 -->
                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Arma tu Cyberdesk paso a paso', 'DESCRIPCION DETALLADA DEL ARMADO DE TU CYBERDESK. PASO 1, PASO 2 Y MONTAJE FINAL DE COMPONENTES.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
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

                <!-- Video Item 3 -->
                <div class="ventana-retro-video video-tutorial" onclick="openVideoModal('Texto texto texto texto', 'DESCRIPCION GENERAL DE CONFIGURACION RETRO Y SOFTWARE PERSONALIZADO.', 'https://www.youtube.com/embed/dQw4w9WgXcQ')">
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
                            <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=500&q=80" alt="Tutorial Retro">
                            <div class="boton-reproducir-circular">
                                <div class="triangulo-reproducir"></div>
                            </div>
                        </div>
                        <p class="titulo-video-tutorial">Texto texto texto texto</p>
                    </div>
                </div>
            </div>

            <!-- Barra visual que indica el avance de la galería de videos. -->
            <div class="barra-progreso-videos">
                <div class="progreso-videos"></div>
            </div>
        </section>

        <!-- ========================================================= -->
        <!-- VISTA 3: galería de tarjetas PDF con su botón de descarga. -->
        <!-- ========================================================= -->
        <section id="seccion-manuales-pdf" class="seccion-vista">
            <h1 class="titulo-pixel brillo-rosa">TUTORIALES EN PDF</h1>

            <!-- Ventana contenedora principal retro -->
            <div class="ventana-principal-pdfs">
                <div class="encabezado-ventana-retro">
                    <span class="titulo-ventana-pdf">Encontra el mejor PDF para vos</span>
                    <div class="puntos-ventana-retro">
                        <span class="punto-ventana-retro"></span>
                        <span class="punto-ventana-retro"></span>
                        <span class="punto-ventana-retro"></span>
                    </div>
                </div>

                <div class="contenido-ventana-pdfs">
                    <div class="rejilla-columnas-pdf">
                        <!-- Columna PDF 1 -->
                        <div class="columna-pdf">
                            <div class="tarjeta-pdf1">
                                <h3>TITULO</h3>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                            </div>
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                        </div>

                        <!-- Columna PDF 2 -->
                        <div class="columna-pdf columna-pdf-desplazada">
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                            <div class="tarjeta-pdf2">
                                <h3>TITULO</h3>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                            </div>
                        </div>

                        <!-- Columna PDF 3 -->
                        <div class="columna-pdf">
                            <div class="tarjeta-pdf3">
                                <h3>TITULO</h3>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                            </div>
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                        </div>

                        <!-- Columna PDF 4 -->
                        <div class="columna-pdf columna-pdf-desplazada">
                            <button class="boton-descargar-pdf">DESCARGAR</button>
                            <div class="tarjeta-pdf4">
                                <h3>TITULO</h3>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                                <p>Descripcion</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ========================================================= -->
    <!-- Modal superpuesto: muestra el video seleccionado y su descripción. -->
    <!-- ========================================================= -->
    <div id="modal-reproduccion-video" class="fondo-modal-video">
        <div class="contenedor-modal-video">
            <button class="boton-cerrar-modal-video" onclick="closeVideoModal()">✕</button>
            <div class="rejilla-contenido-modal-video">
                
                <!-- Reproductor que recibe la URL del video seleccionado. -->
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

                <!-- Panel donde JavaScript actualiza el título y la descripción. -->
                <div class="panel-descripcion-video">
                    <h2 id="titulo-modal-video" class="titulo-brillo-cian">Aprende como funciona la Rassberry pi</h2>
                    <div class="texto-descripcion-video">
                        <p id="descripcion-modal-video">DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION. DESCRIPCION.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Carga la navegación de vistas y el comportamiento del modal de video. -->
    <script src="tutorial.js"></script>
</body>
</html>