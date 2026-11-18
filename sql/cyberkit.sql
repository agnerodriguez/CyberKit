-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-11-2026 a las 02:13:38
-- Versión del servidor: 10.11.16-MariaDB
-- Versión de PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cyberkit`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(8) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`) VALUES
(1, 'dudas'),
(2, 'compartir');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id_comentario` int(8) NOT NULL,
  `id_publicacion` int(8) NOT NULL,
  `id_usuario` int(8) NOT NULL,
  `id_comentario_padre` int(8) DEFAULT NULL,
  `contenido` varchar(255) NOT NULL,
  `imagen` mediumblob DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_comentario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id_comprobante` int(8) NOT NULL,
  `imagen` mediumblob DEFAULT NULL,
  PRIMARY KEY (`id_comprobante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallecompra`
--

CREATE TABLE `detallecompra` (
  `id_detalleCompra` int(8) NOT NULL,
  `id_producto` int(8) NOT NULL,
  `id_usuario` int(8) NOT NULL,
  `id_monto` int(8) NOT NULL,
  `id_comprobante` int(8) NOT NULL,
  `id_estado` int(8) NOT NULL,
  `cantidad` int(3) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detalleCompra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadopago`
--

CREATE TABLE `estadopago` (
  `id_estado` int(8) NOT NULL,
  `nombre` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadopago`
--

INSERT INTO `estadopago` (`id_estado`, `nombre`) VALUES
(1, 'Pago'),
(2, 'Pendiente'),
(3, 'Impago');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monto`
--

CREATE TABLE `monto` (
  `id_monto` int(8) NOT NULL,
  `id_producto` int(8) NOT NULL,
  `importe` decimal(12,2) NOT NULL,
  `fecha_actualizacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_monto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monto`
--

INSERT INTO `monto` (`id_monto`, `id_producto`, `importe`, `fecha_actualizacion`) VALUES
(1, 1, 535545.00, '2026-10-24 18:37:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `stock` int(4) NOT NULL,
  `imagen` varchar(100) NOT NULL,
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `stock`, `imagen`) VALUES
(1, 'CyberKit', 'CyberKit es la solución definitiva para los apasionados del do it yourself y aficionados por la electrónica y la informática. Este kit incluye todo lo necesario para ensamblar tu CyberDesk, ofreciendo una experiencia única de aprendizaje digital.', 86, 'productos/producto_1.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id_publicacion` int(8) NOT NULL,
  `id_usuario` int(8) NOT NULL,
  `id_categoria` int(8) NOT NULL,
  `contenido` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL,
  `imagen` mediumblob DEFAULT NULL,
  PRIMARY KEY (`id_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutoriales`
--

CREATE TABLE `tutoriales` (
  `id_tutorial` int(8) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` varchar(550) NOT NULL,
  `archivo` varchar(350) NOT NULL,
  PRIMARY KEY (`id_tutorial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutoriales`
--

INSERT INTO `tutoriales` (`id_tutorial`, `nombre`, `descripcion`, `archivo`) VALUES
(1, 'Cyberdeck de doble pantalla para armar uno mismo: diseño elegante, máxima funcionalidad.', 'Echa un vistazo a mi último proyecto DIY: un cyberdeck de doble pantalla construido alrededor de una Raspberry Pi 5. Es totalmente imprimible en 3D y de código abierto. En este vídeo te muestro el proceso de diseño y construcción, y luego presento sus características con una GUI personalizada de ttkbootstrap y un experimento de demostración con un sensor I2C.', 'https://youtu.be/cigAxzQGeLg?si=z5yL1T4TlMK2gfxh'),
(2, 'Un ciberdeck curiosamente menta', 'Las latas de Altoids son el contenedor perfecto. Desde kits de supervivencia, sistemas de juegos portátiles personalizados, hasta costura, acuarelas y primeros auxilios, la gente ha estado reutilizando la lata de estas mentas curiosamente fuertes durante décadas. Son latitas muy bonitas. Recientemente me entró un fuerte deseo de construir un pequeño ciberdeck dentro de una de estas latas.', 'https://youtu.be/j262kCYZxZI?si=hML8CJC0rPYnRfYg'),
(3, 'Cyberdeck de sirena para armar', 'Haz tu propia Cyberdesk con una estetica unica de sirena', 'https://youtu.be/aumh4oknVuM?si=i18hChjfVkuSXrDe'),
(4, 'Construcción de cibercubierta con carga solar', 'Te gusta la estetica Cybersolar? este Cyberkit te va a encantar', 'https://youtu.be/sVnjyTWPwnc?si=d1vnaKeRamEE1PHc'),
(5, '¡Construí un Cyberdeck con una laptop vieja y rota!', '¿Qué harías con una laptop vieja y rota que ya nadie quiere?  En lugar de tirarla a la basura, decidí convertirla en algo completamente diferente: ¡un DIY Cyberdeck totalmente funcional!\r\nPara este proyecto desmonté la laptop por completo y rescaté todo el hardware que todavía podía utilizar. La idea era construir un DIY Cyberdeck compacto, portátil y con un diseño inspirado en esas computadoras futuristas de las películas.', 'https://youtu.be/7qXs92ovtMU?si=8muF8Mfvc3PFSu75'),
(6, 'Construyo un PC Cyberdeck Cyberpunk de Bolsillo (Parte 1/2)', 'Mini PC portátil, cyberdeck, Orange Pi, CyberPunk, mini ordenador — Cyberdeck casero inspirado en Neuromancer, más pequeño que un smartphone; caja rugerizada, teclado inalámbrico y batería 18650.\n\nIdeal para makers y aficionados, en el vídeo muestro los retos y soluciones: optimización del espacio, instalación de puertos USB, uso de cables flexibles y acabados estéticos con chapas de aluminio y panel de voltaje. Proyecto práctico y estético para quien quiera un mini ordenador estilo CyberPunk.', 'https://youtu.be/sve-bZJyEYk?si=9pok5-YE5FNB-Fsx');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `edad` int(2) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `fk_comentario_publicacion` (`id_publicacion`),
  ADD KEY `fk_comentario_comentario_padre` (`id_comentario_padre`);

--
-- Indices de la tabla `detallecompra`
--
ALTER TABLE `detallecompra`
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_monto` (`id_monto`),
  ADD KEY `id_comprobante` (`id_comprobante`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `monto`
--
ALTER TABLE `monto`
  ADD KEY `fk_monto_productos` (`id_producto`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id_comentario` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id_comprobante` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detallecompra`
--
ALTER TABLE `detallecompra`
  MODIFY `id_detalleCompra` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estadopago`
--
ALTER TABLE `estadopago`
  MODIFY `id_estado` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `monto`
--
ALTER TABLE `monto`
  MODIFY `id_monto` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id_publicacion` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutoriales`
--
ALTER TABLE `tutoriales`
  MODIFY `id_tutorial` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(8) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_comentario_comentario_padre` FOREIGN KEY (`id_comentario_padre`) REFERENCES `comentario` (`id_comentario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comentario_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detallecompra`
--
ALTER TABLE `detallecompra`
  ADD CONSTRAINT `detallecompra_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `detallecompra_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `detallecompra_ibfk_3` FOREIGN KEY (`id_monto`) REFERENCES `monto` (`id_monto`),
  ADD CONSTRAINT `detallecompra_ibfk_4` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobante` (`id_comprobante`),
  ADD CONSTRAINT `detallecompra_ibfk_5` FOREIGN KEY (`id_estado`) REFERENCES `estadopago` (`id_estado`);

--
-- Filtros para la tabla `monto`
--
ALTER TABLE `monto`
  ADD CONSTRAINT `fk_monto_productos` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `publicacion_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
