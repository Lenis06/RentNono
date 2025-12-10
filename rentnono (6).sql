-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-12-2025 a las 21:59:04
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rentnono`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `propiedad_id` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `favoritos`
--

INSERT INTO `favoritos` (`id`, `usuario_id`, `propiedad_id`, `fecha_agregado`) VALUES
(69, 13, 10, '2025-12-09 01:48:02'),
(70, 13, 8, '2025-12-09 01:48:05'),
(84, 2, 7, '2025-12-09 02:21:47'),
(105, 20, 10, '2025-12-09 16:43:58'),
(107, 21, 10, '2025-12-09 18:47:13'),
(110, 2, 10, '2025-12-10 18:22:09'),
(115, 22, 10, '2025-12-10 20:30:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_actividad`
--

CREATE TABLE `logs_actividad` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(150) DEFAULT NULL,
  `rol` enum('admin','propietario','visitante') NOT NULL DEFAULT 'visitante',
  `accion` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs_actividad`
--

INSERT INTO `logs_actividad` (`id`, `usuario_id`, `usuario_nombre`, `rol`, `accion`, `fecha`) VALUES
(1, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-11-27 09:34:51'),
(2, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-11-27 10:05:31'),
(3, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-11-27 10:19:02'),
(4, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-11-27 10:37:09'),
(5, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-11-27 11:04:48'),
(6, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-11-27 13:19:48'),
(7, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-11-27 13:20:03'),
(8, NULL, 'Administrador', 'admin', 'inhabilitó usuario ID 6 (propietario)', '2025-11-28 00:55:39'),
(9, NULL, 'Administrador', 'admin', 'activó usuario ID 6 (propietario)', '2025-11-28 00:55:41'),
(10, NULL, 'Administrador', 'admin', 'inhabilitó usuario ID 6 (propietario)', '2025-11-28 00:55:46'),
(11, NULL, 'Administrador', 'admin', 'activó usuario ID 6 (propietario)', '2025-11-28 00:55:52'),
(12, NULL, 'Administrador', 'admin', 'inhabilitó usuario ID 6 (propietario)', '2025-11-28 00:55:53'),
(13, NULL, 'Administrador', 'admin', 'activó usuario ID 6 (propietario)', '2025-11-28 00:55:54'),
(14, NULL, 'Administrador', 'admin', 'inhabilitó usuario ID 1 (admin)', '2025-11-28 19:42:52'),
(15, NULL, 'Administrador', 'admin', 'activó usuario ID 1 (admin)', '2025-11-28 19:42:54'),
(16, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-12-03 11:52:54'),
(17, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-12-03 11:53:10'),
(18, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 16:22:06'),
(19, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 16:22:25'),
(20, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-12-08 16:22:45'),
(21, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-12-08 16:23:34'),
(22, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 16:24:46'),
(23, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 16:25:17'),
(24, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-12-08 16:25:26'),
(25, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-12-08 16:26:34'),
(26, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 16:27:50'),
(27, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 16:27:51'),
(28, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 17:03:28'),
(29, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 17:04:26'),
(30, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 17:18:54'),
(31, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 17:20:20'),
(32, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-12-08 17:20:37'),
(33, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-12-08 17:22:07'),
(34, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 17:22:21'),
(35, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 17:24:40'),
(36, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 17:25:06'),
(37, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 17:33:54'),
(38, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 17:55:26'),
(39, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 18:03:03'),
(40, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 18:22:52'),
(41, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 18:25:23'),
(42, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 18:31:09'),
(43, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 18:31:51'),
(44, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 18:43:58'),
(45, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 18:45:48'),
(46, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 19:02:31'),
(47, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 19:16:35'),
(48, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 19:21:56'),
(49, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 19:44:23'),
(50, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 19:46:32'),
(51, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 19:48:21'),
(52, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 19:49:34'),
(53, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 19:51:23'),
(54, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 19:54:03'),
(55, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 20:00:53'),
(56, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 20:07:02'),
(57, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 20:20:12'),
(58, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 20:21:35'),
(59, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 20:34:10'),
(60, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 20:34:21'),
(61, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 20:42:34'),
(62, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 20:44:07'),
(63, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 21:00:03'),
(64, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 21:00:27'),
(65, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 21:05:11'),
(66, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 21:11:04'),
(67, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 21:22:57'),
(68, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 21:23:19'),
(69, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 21:37:45'),
(70, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 21:41:59'),
(71, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 21:44:49'),
(72, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 21:45:23'),
(73, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 21:49:09'),
(74, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 21:49:22'),
(75, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 22:23:48'),
(76, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 22:25:18'),
(77, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 22:44:29'),
(78, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 22:47:08'),
(79, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 22:47:23'),
(80, 13, 'user', 'visitante', 'Inicio de sesión', '2025-12-08 22:47:57'),
(81, 13, 'user', 'visitante', 'Cierre de sesión', '2025-12-08 22:48:28'),
(82, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-12-08 22:48:44'),
(83, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-12-08 22:49:24'),
(84, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 22:56:38'),
(85, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 23:24:55'),
(86, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 23:25:15'),
(87, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-08 23:46:54'),
(88, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-08 23:50:14'),
(89, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-09 10:56:47'),
(90, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-09 11:48:14'),
(91, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-09 11:48:19'),
(92, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-09 13:15:58'),
(93, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-09 13:16:08'),
(94, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-09 13:35:27'),
(95, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-09 13:35:32'),
(96, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-09 13:42:41'),
(97, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-09 13:42:44'),
(98, 20, 'nuevito', 'visitante', 'Registro exitoso', '2025-12-09 13:43:54'),
(99, 20, 'nuevito', 'visitante', 'Cierre de sesión', '2025-12-09 13:44:18'),
(100, 19, 'in gyu', 'visitante', 'Inicio de sesión', '2025-12-09 15:26:48'),
(101, 19, 'in gyu', 'visitante', 'Cierre de sesión', '2025-12-09 15:27:09'),
(102, 21, 'ss', 'visitante', 'Cierre de sesión', '2025-12-09 15:48:07'),
(103, 22, 'leni', 'visitante', 'Cierre de sesión', '2025-12-09 15:59:18'),
(104, 9, 'Juan Perez', 'propietario', 'Cierre de sesión', '2025-12-09 16:26:52'),
(105, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-09 16:30:34'),
(106, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-09 17:00:21'),
(107, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-10 15:21:50'),
(108, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-10 15:22:27'),
(109, 23, 'maira', 'visitante', 'Cierre de sesión', '2025-12-10 15:58:42'),
(110, 1, 'Administrador', 'admin', 'Inicio de sesión', '2025-12-10 15:59:05'),
(111, 1, 'Administrador', 'admin', 'Cierre de sesión', '2025-12-10 16:01:44'),
(112, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-10 17:17:08'),
(113, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-10 17:19:33'),
(114, 22, 'leni', 'visitante', 'Cierre de sesión', '2025-12-10 17:30:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo` enum('reseña','solicitud','otro') DEFAULT 'otro',
  `leido` tinyint(1) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opiniones`
--

CREATE TABLE `opiniones` (
  `id` int(11) NOT NULL,
  `propiedad_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

CREATE TABLE `propiedades` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(12,2) NOT NULL,
  `tipo` enum('casa','departamento','local comercial','terreno o lote','galpon','camping') NOT NULL,
  `operacion` enum('alquiler','venta') NOT NULL,
  `superficie` int(11) DEFAULT NULL,
  `ambientes` int(11) DEFAULT NULL,
  `dormitorios` int(11) DEFAULT NULL,
  `sanitarios` int(11) DEFAULT NULL,
  `garaje` tinyint(1) DEFAULT 0,
  `estado` enum('a estrenar','usado','en construcción') NOT NULL DEFAULT 'a estrenar',
  `ubicacion` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `disponibilidad` enum('disponible','reservado') DEFAULT 'disponible',
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`id`, `titulo`, `descripcion`, `precio`, `tipo`, `operacion`, `superficie`, `ambientes`, `dormitorios`, `sanitarios`, `garaje`, `estado`, `ubicacion`, `direccion`, `disponibilidad`, `imagen`, `fecha_publicacion`, `id_usuario`) VALUES
(7, 'Departamento céntrico', 'Departamento de 2 dormitorios en el centro, cerca de comercios y transporte público.', 45000.00, 'departamento', 'venta', 80, 4, 2, 1, 0, '', 'https://maps.google.com/?q=-29.163,-67.498', 'Av. San Martín 120, Nonogasta', 'disponible', 'departamento_centrico.jpg', '2025-07-15 21:19:36', 1),
(8, 'Casa familiar', 'Casa de 3 dormitorios con patio amplio y garaje, ideal para familias grandes.', 65000.00, 'casa', 'alquiler', 200, 5, 3, 2, 1, 'en construcción', 'https://maps.app.goo.gl/sdjT4VZfZbNdsgCr7', 'Calle Belgrano 450, Nonogasta', 'disponible', 'casa1.jpg', '2025-04-15 21:19:36', 2),
(9, 'Departamento moderno', 'Departamento moderno de 1 dormitorio con todas las comodidades y balcón.', 40000.00, 'departamento', 'alquiler', 60, 3, 1, 1, 0, 'usado', 'https://maps.google.com/?q=-29.165,-67.495', 'Calle Rivadavia 300, Chilecito', 'disponible', 'departamento_moderno.jpeg', '2025-03-15 21:19:36', 1),
(10, 'Casa con jardín', 'Hermosa casa de 3 dormitorios con amplio jardín y quincho para reuniones.', 75000.00, 'casa', 'venta', 250, 6, 3, 2, 1, 'a estrenar', 'https://maps.google.com/?q=-29.160,-67.497', 'Calle Rioja 210, Nonogasta', 'disponible', 'casa_jardin.jpeg', '2025-07-15 21:19:36', 4),
(11, 'Monoambiente amoblado', 'Monoambiente totalmente amoblado, ideal para estudiantes o personas solas.', 30000.00, 'departamento', 'alquiler', 35, 1, 0, 1, 0, 'a estrenar', 'https://maps.google.com/?q=-29.161,-67.492', 'Calle 9 de Julio 50, Nonogasta', 'disponible', 'monoambiente_amoblado.jpg', '2025-02-15 21:19:36', 2),
(12, 'Departamento con terraza', 'Departamento de 2 dormitorios con terraza y vista panorámica a los cerros.', 50000.00, 'departamento', 'alquiler', 90, 4, 2, 1, 0, 'usado', 'https://maps.google.com/?q=-29.166,-67.493', 'Calle Libertad 700, Chilecito', 'disponible', 'departamento_terraza.jpg', '2025-02-15 21:19:36', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo` enum('alquiler','venta') NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `titulo`, `descripcion`, `tipo`, `precio`, `imagen`, `fecha_publicacion`) VALUES
(4, 'Departamento céntrico', 'Departamento de 2 dormitorios en el centro, cerca de transporte y comercios.', 'venta', 45000.00, 'departamento_centrico.jpg', '2025-08-15 21:19:36'),
(5, 'Casa familiar', 'Casa de 3 dormitorios con patio amplio y garaje, ideal para familias.', 'alquiler', 65000.00, 'casa1.jpg', '2025-08-15 21:19:36'),
(6, 'Departamento moderno', 'Departamento moderno de 1 dormitorio con todas las comodidades, cerca de zonas comerciales.', 'alquiler', 40000.00, 'departamento_moderno.jpeg', '2025-08-15 21:19:36'),
(7, 'Casa con jardín', 'Hermosa casa de 3 dormitorios con amplio jardín y garaje, ubicada en zona tranquila y segura.', 'venta', 75000.00, 'casa_jardin.jpeg', '2025-08-15 21:19:36'),
(8, 'Monoambiente amoblado', 'Monoambiente totalmente amoblado, ideal para estudiantes o profesionales.', 'alquiler', 30000.00, 'monoambiente_amoblado.jpg', '2025-08-15 21:19:36'),
(9, 'Departamento con terraza', 'Departamento de 2 dormitorios con terraza y vista panorámica, cerca de transporte público.', 'alquiler', 50000.00, 'departamento_terraza.jpg', '2025-08-15 21:19:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_usuario` varchar(20) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiracion` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_recuperacion`
--

INSERT INTO `tokens_recuperacion` (`id`, `usuario_id`, `tipo_usuario`, `token`, `expiracion`, `usado`, `fecha_creacion`) VALUES
(1, 17, 'visitante', 'a7227e0b9e54b8e2701e3c2b4337c5b4bd75836b335d8e707e3f431404cec93f', '2025-12-09 20:27:20', 0, '2025-12-09 18:27:20'),
(2, 17, 'visitante', '3138e52b4496fb6656018944605e5f850be38b22d200090f8707f8bf4a1889ad', '2025-12-09 20:27:23', 0, '2025-12-09 18:27:23'),
(3, 17, 'visitante', 'a44469f1cc20f8b8571300739ab6db09e9c3d1df62a79e6f380b82faf4f94dbe', '2025-12-09 20:27:26', 0, '2025-12-09 18:27:26'),
(12, 22, 'visitante', '8f32007c30bb0606c4ae1bc8808daf605c5b46538ee1ba9a9637d3ff58572471', '2025-12-10 22:22:49', 1, '2025-12-10 20:22:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `nombre` varchar(30) NOT NULL,
  `sexo` varchar(10) NOT NULL,
  `dni` varchar(30) NOT NULL,
  `correo` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`nombre`, `sexo`, `dni`, `correo`, `telefono`, `password`) VALUES
('nery', 'femenino', 'ererer', 'ererer', 'erer', 'erer'),
('Lenis Riojs', 'masculino', '43344607', 'sdfsd', 'sdfsdf', 'sdfsdf'),
('Lenis Riojs', 'masculino', '43344607', 'sdfsd', 'sdfsdf', 'sdfsdf'),
('Lenis Riojs', 'masculino', '43344607', 'sdfsd', 'sdfsdf', 'sdfsdf'),
('dfgdgdfgdfgfd', 'femenino', 'dfgfdgdf', 'dfgfdg', 'gdfgdfg', 'gdfgdf'),
('Nery jair', 'masculino', '43344607', 'nrt', 'wr', 'wrew'),
('Lenis Riojs', 'masculino', 'dasasd', 'asdasd', 'asdasd', 'asdasd'),
('asdasdas', 'masculino', 'asdas', 'asdasd', 'adasd', 'asdasd'),
('asdas', 'masculino', 'asdasd', 'asdas', 'dasdasd', 'asdasd'),
('LENIS RIOS', 'femenino', '12357545', '5354545', 'ER45454', 'FDGFG');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_admin`
--

CREATE TABLE `usuario_admin` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  `last_activity` datetime DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_admin`
--

INSERT INTO `usuario_admin` (`id`, `nombre`, `correo`, `password_hash`, `telefono`, `foto_perfil`, `role`, `last_activity`, `creado_en`, `estado`) VALUES
(1, 'Administrador', 'admin@rentnono.com', '$2y$10$yDwzuj0IFWkJJSSdqAWlDOS5.Z/NpKH1Emaxz1PfTSHaIS4d9qvby', '3825612630', NULL, 'admin', '2025-11-27 13:34:51', '2025-10-21 09:44:43', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_propietario`
--

CREATE TABLE `usuario_propietario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `sexo` varchar(10) NOT NULL,
  `dni` varchar(30) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `rol` varchar(30) NOT NULL DEFAULT 'propietario',
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_propietario`
--

INSERT INTO `usuario_propietario` (`id`, `nombre`, `sexo`, `dni`, `correo`, `telefono`, `password`, `rol`, `estado`) VALUES
(6, 'Nery Jair Reinoso', 'masculino', '43344607', 'nery.reinoso.7@gmail.com', '3825456521', '0000', 'propietario', 1),
(7, 'JJ', 'femenino', '15768983', 'amelia@gmail.com', '380467892', '1234', 'propietario', 1),
(8, 'Rios Lenis', 'femenino', '47462403', 'lenis@gmail.com', '3825278392', '0000', 'propietario', 1),
(9, 'Juan Perez', 'masculino', '12345678', 'juan@gmil.com', '1234 56-7898', 'ab1db2c639bce74d9853be9f4eb1c7', 'propietario', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_visitante`
--

CREATE TABLE `usuario_visitante` (
  `id` int(100) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'visitante',
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_visitante`
--

INSERT INTO `usuario_visitante` (`id`, `nombre`, `correo`, `password`, `rol`, `estado`) VALUES
(1, 'Nery Jair Reinoso', 'nery.reinoso.7@gmail.com', '1234', 'visitante', 1),
(2, 'Lenis Samira Rios', 'lenis@gmail.com', '0000', 'visitante', 1),
(4, 'Florencia Rios', 'florenciarios@gmail.com', 'Silnemarei22', 'visitante', 1),
(5, 'Graciela Vega', 'gachy@gmail.com', 'gachy', 'visitante', 1),
(7, 'Lucas Ortiz', 'lucas@gmail.com', 'lucas', 'visitante', 1),
(8, 'Arturo Nievas', 'arturo@gmail.com', 'arturo', 'visitante', 1),
(9, 'Ruben Vazquez', 'ruben@gmail.com', 'ruebn', 'visitante', 1),
(10, 'Nuevo Usuario', 'usuario@gmail.com', 'usuario', 'visitante', 1),
(11, 'Root', 'root@gmail.com', 'root', 'visitante', 1),
(12, 'nuevo', 'nuevo@nuevo.com', 'nuevo', 'visitante', 1),
(13, 'user', 'user@gmail.com', 'user', 'visitante', 1),
(14, 'Graciela Mercedes Vega', 'graciela@vega.com', 'graciela', 'visitante', 1),
(15, 'lenis rios', 'lenis@gmail.com', '1234', 'visitante', 1),
(16, 'Mercedes', 'mecha@gmail.com', '01234', 'visitante', 1),
(17, 'In Gyu', 'in.gyu.lenis@gmail.com', 'd7e48742bd76c58476826dcfd56c16', 'visitante', 1),
(18, 'gachy', 'gachy@gmail.com', '074ffa83c5334f7b9764da70eb71ad', 'visitante', 1),
(19, 'in gyu', 'in.gyu.lenis@gmail.com', '3868bc84695b821ba36ef6908b326a', 'visitante', 1),
(20, 'nuevito', 'nuevo@gmail.com', 'ba1c0f229c1452b792e1a46094e67d', 'visitante', 1),
(21, 'ss', 'ss@gamil.com', 'd134e76c45bb99b3cfe04096b3c6c7', 'visitante', 0),
(22, 'leni', 'riosleniz@gmail.com', 'e10adc3949ba59abbe56e057f20f88', 'visitante', 0),
(23, 'maira', 'maira@gmial.com', '368dd3754b48fa78534b749d8aa7f3', 'visitante', 0),
(24, 'maria', 'maria@gmail.com', '32c07619673be970bbbd6df0492e39', 'visitante', 0),
(25, 'jazmin', 'jaz098890@gmail.com', '35093b6c1fa9d754c330ef0e01e9a2', 'visitante', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorito` (`usuario_id`,`propiedad_id`),
  ADD KEY `propiedad_id` (`propiedad_id`);

--
-- Indices de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opiniones`
--
ALTER TABLE `opiniones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indices de la tabla `usuario_propietario`
--
ALTER TABLE `usuario_propietario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_visitante`
--
ALTER TABLE `usuario_visitante`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opiniones`
--
ALTER TABLE `opiniones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuario_propietario`
--
ALTER TABLE `usuario_propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuario_visitante`
--
ALTER TABLE `usuario_visitante`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario_visitante` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario_visitante` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
