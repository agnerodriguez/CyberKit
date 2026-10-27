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

    <div class="encabezado">
        <div class="tituloro">
            <h1 class="titulo">RECYTECH</h1>
            <h3 class="subtitulo">Disfruta de la experiencia de armar</h3>
        </div>
    </div>
     <?php
        include("conexion.php");

        $sql_productos = "SELECT * FROM productos";
        $resultado_productos = mysqli_query($conexion, $sql_productos);
        if ($_FILES['imagen']['size'] > 1000) {
            $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
            // insertar...
        } else {
            echo "Imagen inválida o demasiado pequeña.";
        }
     ?> 
        <section class="products-carousel-section">
            <div class="carousel-container" id="carouselProductos">
                <?php if ($resultado_productos && $resultado_productos->num_rows > 0): ?>
                    <?php while ($row = $resultado_productos->fetch_assoc()): ?>
                        <div class="product-item">
                            <img src="Data:image/png;base64,<?= base64_encode($row['imagen']) ?>"
                                alt="<?= htmlspecialchars($row['nombre']) ?>"
                                class="product-image">
                            <span class="product-name"><?= htmlspecialchars($row['nombre']) ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #00ff66;">No hay productos disponibles.</p>
                <?php endif; ?>
            </div>
<<<<<<< Updated upstream
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
=======
        </section>
>>>>>>> Stashed changes
</body>
</html>