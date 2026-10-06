<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cyberkit - Preguntas</title>
  <!-- Fuente Pixel Art -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #000000;
      color: #ffffff;
      font-family: 'Share Tech Mono', monospace;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    /* Contenedor principal de la pantalla */
    .screen-container {
      width: 100%;
      max-width: 960px;
      background: radial-gradient(circle at center, #1b022b 0%, #05000a 100%);
      border: 1px solid #331040;
      border-radius: 8px;
      padding: 25px 40px;
      box-shadow: 0 0 20px rgba(180, 0, 255, 0.15);
      position: relative;
    }

    /* Barra Superior de Navegación */
    .navbar {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 35px;
    }

    .nav-item {
      color: #ffffff;
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.2s;
    }

    .nav-item:hover, .nav-item.active {
      color: #00f0ff;
      border-bottom: 2px solid #00f0ff;
      padding-bottom: 2px;
    }

    /* Encabezado Principal */
    .header-section {
      margin-bottom: 25px;
    }

    .main-title {
      font-family: 'Press Start 2P', cursive;
      font-size: 2.2rem;
      color: #00f2ff;
      text-shadow: 0 0 10px rgba(0, 242, 255, 0.6);
      letter-spacing: 2px;
      margin-bottom: 10px;
    }

    .subtitle {
      color: #ff007f;
      font-size: 0.8rem;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    /* Pestañas de Categorías */
    .categories-tabs {
      display: flex;
      gap: 12px;
      margin-bottom: 25px;
    }

    .tab-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 10px;
      background: #00818a;
      border: 1px solid #00f2ff;
      color: #ffffff;
      font-family: 'Press Start 2P', cursive;
      font-size: 0.6rem;
      cursor: pointer;
      border-radius: 4px;
      transition: all 0.2s ease;
      text-transform: uppercase;
    }

    .tab-btn.active {
      background: #b00070;
      border-color: #ff00a0;
      box-shadow: 0 0 10px rgba(255, 0, 160, 0.4);
    }

    .tab-icon {
      font-size: 1rem;
    }

    /* Lista de Preguntas */
    .forum-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-bottom: 30px;
    }

    /* Tarjeta estilo Cápsula */
    .post-card {
      background: rgba(10, 5, 20, 0.7);
      border: 1.5 solid #00f2ff;
      border-radius: 25px;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: box-shadow 0.2s;
    }

    /* Estilos de bordes individuales por item (como en el diseño) */
    .post-card:nth-child(1) { border: 1.5px solid #00f2ff; }
    .post-card:nth-child(2) { border: 1.5px solid #ff007f; }
    .post-card:nth-child(3) { border: 1.5px solid #00f2ff; }

    .post-card:hover {
      box-shadow: 0 0 10px rgba(0, 242, 255, 0.3);
    }

    .post-left {
      display: flex;
      align-items: center;
      gap: 15px;
      flex: 1;
    }

    .chat-icon {
      color: #ffffff;
      font-size: 1.2rem;
    }

    .post-title {
      font-family: 'Press Start 2P', cursive;
      font-size: 0.7rem;
      color: #ffffff;
      text-decoration: none;
    }

    .post-author {
      font-size: 0.8rem;
      color: #00f2ff;
      margin-left: 15px;
    }

    .post-card:nth-child(2) .post-author {
      color: #ff007f;
    }

    .divider {
      width: 1px;
      height: 25px;
      background: #ffffff;
      opacity: 0.4;
      margin: 0 15px;
    }

    .post-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .check-icon, .pointer-icon {
      color: #ffffff;
      font-size: 1rem;
    }

    .replies-count {
      font-family: 'Press Start 2P', cursive;
      font-size: 0.75rem;
      color: #00f2ff;
    }

    .post-card:nth-child(2) .replies-count {
      color: #ff007f;
    }

    /* Botón Flotante Inferior */
    .actions-footer {
      display: flex;
      justify-content: flex-end;
    }

    .btn-submit {
      background: #00818a;
      border: 1px solid #00f2ff;
      color: #ffffff;
      font-family: 'Press Start 2P', cursive;
      font-size: 0.55rem;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 0 8px rgba(0, 242, 255, 0.3);
    }

    .btn-submit:hover {
      background: #00f2ff;
      color: #000000;
    }
  </style>
</head>
<body>

  <div class="screen-container">
    
    <!-- Navegación Superior -->
    <nav class="navbar">
      <a href="#" class="nav-item">Inicio</a>
      <a href="#" class="nav-item">Comprar</a>
      <a href="#" class="nav-item">Tutoriales</a>
      <a href="#" class="nav-item active">Nuestro foro</a>
      <a href="#" class="nav-item">Nosotros</a>
      <a href="#" class="nav-item">Cuenta</a>
    </nav>

    <!-- Header Sección -->
    <div class="header-section">
      <h1 class="main-title">PREGUNTAS</h1>
      <p class="subtitle">¡HAZ LAS CONSULTAS NECESARIAS Y ALGUIEN DE LA COMUNIDAD TE RESPONDERÁ!</p>
    </div>

    <!-- Pestañas de Secciones -->
    <div class="categories-tabs">
      <button class="tab-btn active">
        <span class="tab-icon">❓</span> HACE TU PREGUNTA
      </button>
      <button class="tab-btn">
        <span class="tab-icon">⭐</span> SUBE RECOMENDACIONES
      </button>
      <button class="tab-btn">
        <span class="tab-icon">⚙️</span> SUBE TU KIT ARMADO!
      </button>
    </div>

    <!-- Tarjetas de Publicaciones -->
    <div class="forum-list">
      
      <div class="post-card">
        <div class="post-left">
          <span class="chat-icon">💬</span>
          <a href="#" class="post-title">¿AQUÍ ESTA LA PREGUNTA?</a>
          <span class="post-author">• @NOMBRE</span>
        </div>
        <span class="pointer-icon">👆</span>
        <div class="divider"></div>
        <div class="post-right">
          <span class="check-icon">☑️</span>
          <span class="replies-count">5 RESPUESTAS</span>
        </div>
      </div>

      <div class="post-card">
        <div class="post-left">
          <span class="chat-icon">💬</span>
          <a href="#" class="post-title">¿AQUÍ ESTA LA PREGUNTA?</a>
          <span class="post-author">• @NOMBRE</span>
        </div>
        <span class="pointer-icon">👆</span>
        <div class="divider"></div>
        <div class="post-right">
          <span class="check-icon">☑️</span>
          <span class="replies-count">5 RESPUESTAS</span>
        </div>
      </div>

      <div class="post-card">
        <div class="post-left">
          <span class="chat-icon">💬</span>
          <a href="#" class="post-title">¿AQUÍ ESTA LA PREGUNTA?</a>
          <span class="post-author">• @NOMBRE</span>
        </div>
        <span class="pointer-icon">👆</span>
        <div class="divider"></div>
        <div class="post-right">
          <span class="check-icon">☑️</span>
          <span class="replies-count">5 RESPUESTAS</span>
        </div>
      </div>
    </div>

    <!-- Botón Crear Pregunta -->
    <div class="actions-footer">
      <button class="btn-submit">
        SUBI TU PREGUNTA 👆
      </button>
    </div>

  </div>

</body>
</html>