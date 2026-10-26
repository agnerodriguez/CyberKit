<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="inicio.css">
</head>
<body>
    <?php include("nav.php"); ?>

    <!-- 1. ENCABEZADO EN CAJA ROSA -->
    <div class="encabezado">
        <div class="title-box-pink">
            <h1 class="titulo">RECYTECH</h1>
            <h3 class="subtitulo">Disfruta de la experiencia de armar</h3>
        </div>
    </div>

    <!-- 2. CARRUSEL DE PRODUCTOS (PHP Mezclado) -->
    <section class="products-carousel-section">
        <div class="carousel-container" id="carouselProductos">
            <?php 
                include("conexion.php"); // Asegúrate de ajustar la ruta si tu archivo de conexión se llama distinto
                
                $sql_productos = "SELECT * FROM productos";
                $resultado_productos = mysqli_query($conexion, $sql_productos);

                if ($resultado_productos && $resultado_productos->num_rows > 0):
                    while ($row = $resultado_productos->fetch_assoc()): 
            ?>
                        <div class="product-item">
                            <!-- Ajusta 'imagenes/' según la carpeta donde tengas tus imágenes -->
                            <img src="imagenes/<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="product-image">
                            <span class="product-name"><?= htmlspecialchars($row['nombre']) ?></span>
                        </div>
            <?php 
                    endwhile; 
                else:
            ?>
                    <p style="color: #00ff66;">No hay productos disponibles.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- 3. SECCIÓN ESTÁTICA -->
    <section class="info-section">
        <div class="grid-container">
            
            <!-- Fila 1: Imagen izquierda, Texto derecha -->
            <div class="info-card image-card">
                <img src="fondos/seccion1.png" alt="Proyecto 1">
            </div>
            <div class="info-card text-card">
                <h2 class="card-title">Escribe tu sección aquí</h2>
                <span class="card-subtitle">[SUBTÍTULO 01]</span>
                <p class="card-text">
                    El contenido visual es un lenguaje transversal, universal, como la música. 
                    Somos capaces de entender imágenes de hace millones de años, incluso de otras culturas.
                </p>
            </div>

            <!-- Fila 2: Texto izquierda, Imagen derecha -->
            <div class="info-card text-card">
                <h2 class="card-title">Escribe tu sección aquí</h2>
                <span class="card-subtitle">[SUBTÍTULO 02]</span>
                <p class="card-text">
                    El contenido visual es un lenguaje transversal, universal, como la música. 
                    Somos capaces de entender imágenes de hace millones de años, incluso de otras culturas.
                </p>
            </div>
            <div class="info-card image-card">
                <img src="fondos/seccion2.png" alt="Proyecto 2">
            </div>

        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>