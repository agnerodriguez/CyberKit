<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cyberkit - Nuestro Foro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Michroma&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="foro.css">
</head>
<body>

  <!-- SECTION INICIO -->
  <section class="inicio">
    <?php include 'nav.php'; ?>
    <div class="cyber-content">
      <header class="foro-header">
        <h2>¡SÉ PARTE DE LA CYBER-COMUNIDAD!</h2>
        <h1>NUESTRO FORO</h1>
      </header>
  </section>

  <!-- SECCIÓN FORO -->
  <section class="foro-seccion">
    
    <!-- CABECERA ALINEADA A LA IZQUIERDA -->
    <div class="foro-controles-top">
      <div class="preguntas-header">
        <h1>Comunidad Cyberkit</h1>
        <h2>¡Consulta tus dudas o comparte los resultados de tu kit!</h2>
      </div>

      <div class="foro-top-bar">
        <div class="filtro-categoria">
          <label for="selectCategoria">Ver sección:</label>
          <select id="selectCategoria" onchange="cargarPublicaciones()">
            <option value="1">Dudas</option>
            <option value="2">Compartir (Proyectos / Kits)</option>
          </select>
        </div>

        <!-- Botón para alternar la visibilidad del formulario -->
        <button class="btn-cyber" onclick="alternarFormulario()">+ Hacer tu publicación</button>
      </div>
    </div>

    <!-- FORMULARIO OCULTO INICIALMENTE -->
    <div id="contenedorFormularioPost" class="crear-post-card" style="display: none;">
      <h3>Crear nueva publicación</h3>
      
      <div style="margin-bottom: 10px;">
        <label for="postCategoriaSelect" style="margin-right: 10px; font-size: 0.8rem;">Publicar en:</label>
        <select id="postCategoriaSelect" style="padding: 5px;">
          <option value="1">Dudas</option>
          <option value="2">Compartir (Proyectos / Kits)</option>
        </select>
      </div>

      <textarea id="nuevoPostContenido" placeholder="Escribe tu duda o describe tu proyecto/kit..."></textarea>
      
      <div style="margin-bottom: 10px;">
        <label for="nuevoPostImagen" style="font-size: 0.8rem; color: #ff00ff;">Adjuntar foto (opcional para tu kit):</label><br>
        <input type="file" id="nuevoPostImagen" accept="image/*" style="margin-top: 5px;">
      </div>

      <button class="btn-cyber" onclick="crearPublicacion()">PUBLICAR</button>
    </div>

    <!-- CONTENEDOR DE LISTA DE PUBLICACIONES Y COMENTARIOS (ABAJO) -->
    <div id="preguntasLista" class="preguntas-lista" style="margin-top: 20px;"></div>
  </section>

  <script src="foro.js"></script>
</body>
<?php include 'footer.php'; ?>
</html>