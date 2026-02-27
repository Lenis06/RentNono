-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-02-2026 a las 02:31:47
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
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `nombre`, `correo`, `password`, `fecha_registro`) VALUES
(1, 'Administrador', 'admin@rentnono.com', '0192023a7bbd73250516f069df18b500', '2026-02-25 08:00:06');

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
(84, 2, 7, '2025-12-09 02:21:47'),
(120, 32, 12, '2026-02-19 18:01:16'),
(121, 32, 7, '2026-02-19 18:02:19'),
(123, 33, 9, '2026-02-24 19:42:12'),
(125, 33, 7, '2026-02-24 19:47:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_propiedades`
--

CREATE TABLE `imagenes_propiedades` (
  `id` int(11) NOT NULL,
  `id_propiedad` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `es_principal` tinyint(4) DEFAULT 0,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

CREATE TABLE `intentos_login` (
  `id` int(11) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `intentos` int(11) DEFAULT 1,
  `ultimo_intento` datetime NOT NULL,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(114, 22, 'leni', 'visitante', 'Cierre de sesión', '2025-12-10 17:30:28'),
(115, 2, 'Lenis Samira Rios', 'visitante', 'Inicio de sesión', '2025-12-11 09:27:14'),
(116, 2, 'Lenis Samira Rios', 'visitante', 'Cierre de sesión', '2025-12-11 09:28:59'),
(117, 26, 'jazmin', 'visitante', 'Registro exitoso', '2025-12-11 11:39:24'),
(118, 27, 'lenisve', 'visitante', 'Registro exitoso', '2025-12-11 11:51:21'),
(119, 28, 'leni', 'visitante', 'Registro exitoso', '2025-12-11 11:52:37'),
(120, 29, 'lenis', 'visitante', 'Registro exitoso', '2025-12-11 12:36:43'),
(121, 29, 'lenis', 'visitante', 'Cierre de sesión', '2025-12-11 12:47:27'),
(122, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 14:26:26'),
(123, 26, 'Jazmin Ormeño', 'visitante', 'Cierre de sesión', '2025-12-11 14:29:10'),
(124, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 14:47:08'),
(125, 26, 'Jazmin Ormeño', 'visitante', 'Cierre de sesión', '2025-12-11 14:47:15'),
(126, 10, 'Usuario Google', 'propietario', 'Registro/Login con Google', '2025-12-11 14:48:41'),
(127, 10, 'Usuario Google', 'propietario', 'Cierre de sesión', '2025-12-11 14:53:14'),
(128, 28, 'Lenis Rios', 'visitante', 'Inicio de sesión con Google', '2025-12-11 14:53:38'),
(129, 28, 'Lenis Rios', 'visitante', 'Cierre de sesión', '2025-12-11 14:53:51'),
(130, 29, 'Lenis Rios', 'visitante', 'Inicio de sesión con Google', '2025-12-11 14:54:38'),
(131, 29, 'Lenis Rios', 'visitante', 'Cierre de sesión', '2025-12-11 14:55:49'),
(132, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 15:06:55'),
(133, 26, 'Jazmin Ormeño', 'visitante', 'Cierre de sesión', '2025-12-11 15:10:57'),
(134, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 15:38:41'),
(135, 26, 'Jazmin Ormeño', 'visitante', 'Cierre de sesión', '2025-12-11 15:56:01'),
(136, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 15:56:18'),
(137, 26, 'Jazmin Ormeño', 'visitante', 'Cierre de sesión', '2025-12-11 16:02:18'),
(138, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 16:17:44'),
(139, 26, 'Jazmin Ormeño', 'visitante', 'Cierre de sesión', '2025-12-11 16:27:48'),
(140, 10, 'Usuario Google', 'propietario', 'Registro/Login con Google', '2025-12-11 16:29:07'),
(141, 10, 'Usuario Google', 'propietario', 'Cierre de sesión', '2025-12-11 16:29:14'),
(142, 26, 'Jazmin Ormeño', 'visitante', 'Inicio de sesión con Google', '2025-12-11 16:30:07'),
(143, NULL, 'Jazmin Ormeño', 'visitante', 'Eliminó cuenta permanentemente', '2025-12-11 16:30:14'),
(144, NULL, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-20 12:47:25'),
(145, NULL, 'Admin Regular', 'admin', 'Inicio de sesión en panel admin', '2026-02-20 12:48:28'),
(146, NULL, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-20 12:50:58'),
(147, NULL, 'Admin Regular', 'admin', 'Inicio de sesión en panel admin', '2026-02-20 12:52:09'),
(148, NULL, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-25 00:37:43'),
(149, NULL, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-25 00:38:42'),
(150, NULL, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-25 01:08:33'),
(151, 2, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-26 14:43:27'),
(152, 2, 'Super Administrador', 'admin', 'Inicio de sesión en panel admin', '2026-02-26 14:56:41');

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

--
-- Volcado de datos para la tabla `opiniones`
--

INSERT INTO `opiniones` (`id`, `propiedad_id`, `usuario_id`, `rating`, `comentario`, `fecha`, `estado`) VALUES
(1, 8, 32, 4, 'bella casa, mencanto', '2026-02-20 01:51:14', ''),
(2, 7, 32, 3, 'fq', '2026-02-20 01:51:31', ''),
(3, 12, 32, 4, 'genial', '2026-02-20 05:00:09', ''),
(4, 11, 32, 5, 'excelente', '2026-02-20 05:00:29', ''),
(5, 8, 33, 1, 'no me gustó', '2026-02-24 19:41:09', '');

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
  `id_usuario` int(11) DEFAULT NULL,
  `visitas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`id`, `titulo`, `descripcion`, `precio`, `tipo`, `operacion`, `superficie`, `ambientes`, `dormitorios`, `sanitarios`, `garaje`, `estado`, `ubicacion`, `direccion`, `disponibilidad`, `imagen`, `fecha_publicacion`, `id_usuario`, `visitas`) VALUES
(7, 'Departamento céntrico', 'Departamento de 2 dormitorios en el centro, cerca de comercios y transporte público.', 45000.00, 'departamento', 'venta', 80, 4, 2, 1, 0, '', 'https://maps.google.com/?q=-29.163,-67.498', 'Av. San Martín 120, Nonogasta', 'disponible', 'departamento_centrico.jpg', '2025-07-15 21:19:36', 1, 19),
(8, 'Casa familiar', 'Casa de 3 dormitorios con patio amplio y garaje, ideal para familias grandes.', 65000.00, 'casa', 'alquiler', 200, 5, 3, 2, 1, 'en construcción', 'https://maps.app.goo.gl/sdjT4VZfZbNdsgCr7', 'Calle Belgrano 450, Nonogasta', 'disponible', 'casa1.jpg', '2025-04-15 21:19:36', 2, 82),
(9, 'Departamento moderno', 'Departamento moderno de 1 dormitorio con todas las comodidades y balcón.', 40000.00, 'departamento', 'alquiler', 60, 3, 1, 1, 0, 'usado', 'https://maps.google.com/?q=-29.165,-67.495', 'Calle Rivadavia 300, Chilecito', 'disponible', 'departamento_moderno.jpeg', '2025-03-15 21:19:36', 1, 17),
(11, 'Monoambiente amoblado', 'Monoambiente totalmente amoblado, ideal para estudiantes o personas solas.', 30000.00, 'departamento', 'alquiler', 35, 1, 0, 1, 0, 'a estrenar', 'https://maps.google.com/?q=-29.161,-67.492', 'Calle 9 de Julio 50, Nonogasta', 'disponible', 'monoambiente_amoblado.jpg', '2025-02-15 21:19:36', 2, 7),
(12, 'Departamento con terraza', 'Departamento de 2 dormitorios con terraza y vista panorámica a los cerros.', 50000.00, 'departamento', 'alquiler', 90, 4, 2, 1, 0, 'usado', 'https://maps.google.com/?q=-29.166,-67.493', 'Calle Libertad 700, Chilecito', 'disponible', 'departamento_terraza.jpg', '2025-02-15 21:19:36', 1, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades_imagenes`
--

CREATE TABLE `propiedades_imagenes` (
  `id` int(11) NOT NULL,
  `propiedad_id` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT 'fa-solid fa-star',
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `icono`, `estado`, `fecha_creacion`) VALUES
(1, 'wifi', 'fa-solid fa-wifi', 1, '2026-02-20 18:47:26'),
(2, 'cochera', 'fa-solid fa-car', 1, '2026-02-20 18:47:26'),
(3, 'patio', 'fa-solid fa-tree', 1, '2026-02-20 18:47:26'),
(4, 'amoblado', 'fa-solid fa-couch', 1, '2026-02-20 18:47:26'),
(5, 'aire acondicionado', 'fa-solid fa-snowflake', 1, '2026-02-20 18:47:26'),
(6, 'calefacción', 'fa-solid fa-fire', 1, '2026-02-20 18:47:26'),
(7, 'cable TV', 'fa-solid fa-tv', 1, '2026-02-20 18:47:26'),
(8, 'pileta', 'fa-solid fa-swimming-pool', 1, '2026-02-20 18:47:26'),
(9, 'seguridad 24hs', 'fa-solid fa-shield-alt', 1, '2026-02-20 18:47:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_usuario` enum('visitante','propietario','admin') NOT NULL DEFAULT 'visitante',
  `token` varchar(64) NOT NULL,
  `expiracion` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_recuperacion`
--

INSERT INTO `tokens_recuperacion` (`id`, `usuario_id`, `tipo_usuario`, `token`, `expiracion`, `usado`, `fecha_creacion`) VALUES
(1, 32, 'visitante', 'c791feda239a839a08f49d55d1225bc711b5133a258090fa6084d374b9e192f8', '2026-02-20 01:42:33', 0, '2026-02-20 03:42:33'),
(2, 11, 'propietario', 'eddf9a3e54d311afde2c5b062189207b9fcf49c57ec3d24c22961cd88ace5ca6', '2026-02-20 02:15:28', 0, '2026-02-20 04:15:28'),
(3, 11, 'propietario', 'c8e6849aaae91e39ef46ed5259b44c403ae6e44383d611e661b0dd7ddad85991', '2026-02-20 15:06:48', 0, '2026-02-20 20:06:48'),
(4, 11, 'propietario', '8ca47371e171fe11decd576d2b4f5ba7f633422d39a113ebea429178e94fcc7c', '2026-02-20 15:06:51', 0, '2026-02-20 20:06:51');

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
(1, 'Administrador', 'admin@rentnono.com', '$2y$10$yDwzuj0IFWkJJSSdqAWlDOS5.Z/NpKH1Emaxz1PfTSHaIS4d9qvby', '3825612630', NULL, 'admin', '2025-11-27 13:34:51', '2025-10-21 09:44:43', 1),
(2, 'Super Administrador', 'rentnono.oficial@gmail.com', '$2y$10$7E7kvGGw66XJn2iUjWMv9eUAJX2TK1WL9zziHdtQTyESZYXhCWyb2', NULL, NULL, 'superadmin', NULL, '2026-02-20 12:30:13', 1),
(3, 'Admin Regular', 'jazzmin1221rm@gmail.com', '$2y$10$uXg9qCxDCwfWbkmX8nWZb.XuMTTrTsZzl6n.qxnBLgaSyanTKwgfC', NULL, NULL, 'admin', NULL, '2026-02-20 12:44:11', 1);

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
  `password` varchar(255) DEFAULT NULL,
  `rol` varchar(30) NOT NULL DEFAULT 'propietario',
  `estado` tinyint(1) DEFAULT 1,
  `google_id` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_propietario`
--

INSERT INTO `usuario_propietario` (`id`, `nombre`, `sexo`, `dni`, `correo`, `telefono`, `password`, `rol`, `estado`, `google_id`, `foto`) VALUES
(6, 'Nery Jair Reinoso', 'masculino', '43344607', 'nery.reinoso.7@gmail.com', '3825456521', 'e10adc3949ba59abbe56e057f20f88', 'propietario', 1, NULL, NULL),
(7, 'JJ', 'femenino', '15768983', 'amelia@gmail.com', '380467892', 'e10adc3949ba59abbe56e057f20f88', 'propietario', 1, NULL, NULL),
(8, 'Rios Lenis', 'femenino', '47462403', 'lenis@gmail.com', '3825278392', 'e10adc3949ba59abbe56e057f20f88', 'propietario', 1, NULL, NULL),
(9, 'Juan Perez', 'masculino', '12345678', 'juan@gmil.com', '1234 56-7898', 'e10adc3949ba59abbe56e057f20f88', 'propietario', 0, NULL, NULL),
(10, 'Usuario Google', 'femenino', '12345677', 'usuario@gmail.com', '3825 43-5674', NULL, 'propietario', 1, 'google_njtgrstq4', 'https://lh3.googleusercontent.com/a/ACg8ocKnJ93w895kHa4-D4AR7Wdrcta1plSPyc8fO1e4a9PvbLEnjYby=s96-c'),
(11, 'Jazmin Ormeño', '', '', 'jaz098890@gmail.com', '', 'google_auth', 'propietario', 0, '114745559780477726660', NULL),
(12, 'Juan Pérez', 'masculino', '12345678', 'juan@email.com', '3825 12-3456', '677958a003e7f0fb9cc48c8ce8f1498a', 'propietario', 1, NULL, NULL),
(13, 'Lenis Rios', '', '', 'lenisrios06@gmail.com', '', 'google_auth', 'propietario', 0, '113579988859093683325', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_visitante`
--

CREATE TABLE `usuario_visitante` (
  `id` int(100) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'visitante',
  `estado` tinyint(1) DEFAULT 1,
  `google_id` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_visitante`
--

INSERT INTO `usuario_visitante` (`id`, `nombre`, `correo`, `password`, `rol`, `estado`, `google_id`, `foto`, `telefono`) VALUES
(1, 'Nery Jair Reinoso', 'nery.reinoso.7@gmail.com', 'e10adc3949ba59abbe56e057f20f88', 'visitante', 1, NULL, NULL, NULL),
(2, 'Lenis Samira Rios', 'lenis@gmail.com', '4a7d1ed414474e4033ac29ccb8653d', 'visitante', 1, NULL, NULL, NULL),
(32, 'In gyu', 'in.gyu.lenis@gmail.com', 'google_auth', 'visitante', 0, '115765506390698475208', NULL, NULL),
(33, 'Leniz Rios', 'riosleniz@gmail.com', 'google_auth', 'visitante', 0, '109727641250122687732', NULL, NULL),
(34, 'María González', 'maria@email.com', '8c250de46e6ff824a51ec0765e8a822b', 'visitante', 1, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorito` (`usuario_id`,`propiedad_id`),
  ADD KEY `propiedad_id` (`propiedad_id`);

--
-- Indices de la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_propiedad` (`id_propiedad`);

--
-- Indices de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_correo_ip` (`correo`,`ip_address`);

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
-- Indices de la tabla `propiedades_imagenes`
--
ALTER TABLE `propiedades_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propiedad_id` (`propiedad_id`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_usuario_tipo` (`usuario_id`,`tipo_usuario`),
  ADD KEY `idx_expiracion` (`expiracion`),
  ADD KEY `idx_usado` (`usado`);

--
-- Indices de la tabla `usuario_propietario`
--
ALTER TABLE `usuario_propietario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Indices de la tabla `usuario_visitante`
--
ALTER TABLE `usuario_visitante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opiniones`
--
ALTER TABLE `opiniones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `propiedades_imagenes`
--
ALTER TABLE `propiedades_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario_propietario`
--
ALTER TABLE `usuario_propietario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuario_visitante`
--
ALTER TABLE `usuario_visitante`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
-- Filtros para la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD CONSTRAINT `imagenes_propiedades_ibfk_1` FOREIGN KEY (`id_propiedad`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario_visitante` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propiedades_imagenes`
--
ALTER TABLE `propiedades_imagenes`
  ADD CONSTRAINT `propiedades_imagenes_ibfk_1` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
