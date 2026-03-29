-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-03-2026 a las 01:09:40
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
-- Base de datos: `crm_bahari`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `departamento` varchar(255) NOT NULL,
  `edad` int(11) NOT NULL,
  `fecha_registro` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `departamento`, `edad`, `fecha_registro`) VALUES
(1, 'David Caro', 'Nuevo León', 35, '2026-02-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `smtp_host` varchar(128) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `smtp_user` varchar(128) DEFAULT NULL,
  `smtp_pass` varchar(500) DEFAULT NULL COMMENT 'Cifrado AES-256',
  `notificaciones_ganado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `notificaciones_ganado`) VALUES
(1, 'smtp.hostinger.com', 465, 'software@bahariaqua.com', '8GOIGnqXtaNwiI5v9dUa2DBBRDNkckpKU0U0U1BLTHFFekJ0R2p2MWlMazdqVEUvNC9wY0E2NkJHS0k9', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `empresa_id`, `nombre`, `cargo`, `email`, `telefono`) VALUES
(2, 31, 'David Caro', 'Contador', 'dacamo0502@gmail.com', '3009876543'),
(4, 47, 'David Caro', 'Contador', 'dacamo0502@gmail.com', '3065437890'),
(6, 39, 'Persona ', 'Persona', 'persona@email.com', '3113111234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `dpto` varchar(50) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `actividad_economica` varchar(100) DEFAULT NULL,
  `correo_comercial` varchar(100) DEFAULT NULL,
  `aplica` varchar(10) DEFAULT NULL,
  `etapa_venta` enum('prospectado','contactado','negociacion','seguimiento','ganado','perdido') DEFAULT 'prospectado',
  `observaciones` text DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `razon_social`, `dpto`, `ciudad`, `actividad_economica`, `correo_comercial`, `aplica`, `etapa_venta`, `observaciones`, `usuario_id`, `creado_en`) VALUES
(31, 'SCHAEFER CHARTERING COLOMBIA S.A.S.', 'ATLANTICO', 'BARRANQUILLA', 'OTRAS ACTIV COMP/TRANSP', 'accounting@schaefer-bma.com', 'SI', 'contactado', '--- Info Importada ---\r\nFuente Tel: GOOGLE.', 1, '2026-02-24 15:13:43'),
(36, 'TRANSPORTES PEREZ PUERTO', 'ATLANTICO', 'SOLEDAD', 'TRANSP CARGA/CARRETERA', 'rh.gloriapuerto@gmail.com', '', 'prospectado', '', 1, '2026-02-24 15:13:43'),
(38, 'EL PIZARRON', 'SANTANDER', 'BUCARAMANGA', 'TRANSP CARGA/CARRETERA', 'nandrearr@gmail.com', 'SI', 'perdido', 'Empresa solicitó le envíen la oferta al correo y reunirse de forma virtual para conocer más', 1, '2026-02-24 15:13:43'),
(39, 'MUDANZAS REYES', 'BOGOTA', 'BOGOTA 1', 'TRANSP CARGA/CARRETERA', 'mudanzasreyes@hotmail.com', 'SI', 'seguimiento', '', 1, '2026-02-24 15:13:43'),
(41, 'TRANSPORTE IG', 'VALLE DEL CAUCA', 'CALI', 'TRANSP CARGA/CARRETERA', 'ingridcali16@gmail.com', '', 'negociacion', '', 1, '2026-02-24 15:13:43'),
(43, 'TRANSLIPER LIMITADA', 'ANTIOQUIA', 'MEDELLIN', 'TRANSP CARGA/CARRETERA', 'cenatac_ltda@hotmail.com', 'NO', 'perdido', '', 1, '2026-02-24 15:13:43'),
(45, 'TRANSPORTES & SOLUCIONES LOGISTICAS TRANSLOG S.A.S.', 'ATLANTICO', 'BARRANQUILLA', 'TRANSP CARGA/CARRETERA', 'transologsas@gmail.com', '', 'prospectado', '', 1, '2026-02-24 15:13:43'),
(46, 'TURSENLUV', 'BOGOTA', 'BOGOTA 1', 'TRANSP CARGA/CARRETERA', 'tursenluv@gmail.com', '', 'prospectado', '', 1, '2026-02-24 15:13:43'),
(47, 'ELITE LOGISTICA SAN JOSE DEL GUAVIARE', 'GUAVIARE', 'SAN JOSE DEL GUAVIARE', 'TRANSP CARGA/CARRETERA', 'contabilidad2@elitelogistica.com', 'SI', 'contactado', 'Se realizó contacto con David Caro, el contador de la empresa. Solicitó conocer más a detalle la oferta de servicios\r\n', 1, '2026-02-24 15:13:43'),
(48, 'PARQUEADERO TEUSACA', 'CUNDINAMARCA', 'LA CALERA', 'TRANSP CARGA/CARRETERA', 'wirima@hotmail.com', '', 'ganado', '', 1, '2026-02-24 15:13:43'),
(49, 'TRANSPORTE MAYLI', 'TOLIMA', 'IBAGUE', 'TRANSP CARGA/CARRETERA', 'maritos2609@hotmail.com', '', 'prospectado', '', 1, '2026-02-24 15:13:43'),
(50, 'TRANSPORTES INTEGRALES DE ORIENTE SAS - SAN MARCOS', 'SUCRE', 'SAN MARCOS', 'TRANSP CARGA/CARRETERA', 'transportestio@gmail.com', '', 'prospectado', '', 1, '2026-02-24 15:13:43'),
(58, 'TRASTEOS Y TRANSPORTES DIOMAR', 'VALLE DEL CAUCA', 'CALI', 'TRANSP CARGA/CARRETERA', 'rodrigosanchez198@gmail.com', '', 'prospectado', '', 1, '2026-02-24 15:13:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `integraciones`
--

CREATE TABLE `integraciones` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL COMMENT 'ej: whatsapp, google_calendar',
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo_auth` enum('api_key','oauth','url','none') DEFAULT 'none',
  `estado` enum('inactiva','activa','error') DEFAULT 'inactiva',
  `campos` text DEFAULT NULL COMMENT 'JSON con los campos configurados (cifrados)',
  `error_msg` text DEFAULT NULL COMMENT '├Ültimo error registrado',
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `integraciones`
--

INSERT INTO `integraciones` (`id`, `slug`, `nombre`, `descripcion`, `tipo_auth`, `estado`, `campos`, `error_msg`, `actualizado_en`, `creado_en`) VALUES
(1, 'whatsapp', 'WhatsApp', 'Enviar mensajes v├¡a WhatsApp (wa.me o API)', 'api_key', 'inactiva', 'gyHcUD9hKEiLbnwd1Kz8B2kwNklKU3kyYmppNzFhSGFxbmVrb0JHTEtQUmhtNHBnemtaNWZLc29YNXZGQTlYZ2haYms3SXB4YU1TVUtvTWk=', NULL, '2026-02-24 08:42:09', '2026-02-23 23:07:10'),
(2, 'google_calendar', 'Google Calendar', 'Sincronizar eventos con Google Calendar', 'oauth', 'inactiva', NULL, NULL, '2026-02-23 23:07:10', '2026-02-23 23:07:10'),
(3, 'google_maps', 'Google Maps', 'Autocompletar y visualizar direcciones', 'api_key', 'inactiva', NULL, NULL, '2026-02-23 23:07:10', '2026-02-23 23:07:10'),
(4, 'webhook_generico', 'Webhook Gen├®rico', 'Enviar eventos a una URL externa (n8n, etc.)', 'url', 'inactiva', NULL, NULL, '2026-02-23 23:07:10', '2026-02-23 23:07:10'),
(5, 'zapier', 'Zapier', 'Conectar con miles de aplicaciones via Zapier', 'api_key', 'inactiva', NULL, NULL, '2026-02-23 23:07:10', '2026-02-23 23:07:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'ej: venta_ganada, empresa_creada',
  `titulo` varchar(150) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `url_accion` varchar(255) DEFAULT NULL COMMENT 'URL a donde redirigir al hacer click',
  `leida` tinyint(1) DEFAULT 0,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `titulo`, `mensaje`, `url_accion`, `leida`, `creado_en`) VALUES
(1, 1, 'cambio_etapa', 'Cambio de etapa: EMPRESA ABC', 'Superadmin movió \"EMPRESA ABC\" de Ganado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 08:56:24'),
(2, 1, 'cambio_etapa', 'Cambio de etapa: SCHAEFER CHARTERING COLOMBIA S.A.S.', 'Superadmin movió \"SCHAEFER CHARTERING COLOMBIA S.A.S.\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 13:55:48'),
(3, 1, 'cambio_etapa', 'Cambio de etapa: COMPRA Y VENTA MATERIAL DE SEGUNDA LAS DOS RR', 'Superadmin movió \"COMPRA Y VENTA MATERIAL DE SEGUNDA LAS DOS RR\" de Prospectado → Negociacion.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 13:56:00'),
(4, 1, 'venta_ganada', '¡Oportunidad ganada! GLOBAL CARGAS LOGISTICS', 'Superadmin cerró la oportunidad de \"GLOBAL CARGAS LOGISTICS\" como GANADA.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 13:56:12'),
(5, 1, 'cambio_etapa', 'Cambio de etapa: TRANSPORTES PEREZ PUERTO', 'Superadmin movió \"TRANSPORTES PEREZ PUERTO\" de Prospectado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 13:56:21'),
(6, 1, 'venta_ganada', 'Venta registrada: GLOBAL CARGAS LOGISTICS', 'Superadmin registró una venta de $15,000,000.00 el 2026-02-24.', 'http://localhost/crm-php.com/index.php?controller=venta&action=index', 1, '2026-02-24 13:59:44'),
(7, 1, 'venta_ganada', '¡Oportunidad ganada! GRU & TAX CENTER SAS', 'David Caro Morales cerró la oportunidad de \"GRU & TAX CENTER SAS\" como GANADA.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 14:42:01'),
(8, 4, 'venta_ganada', '¡Oportunidad ganada! GRU & TAX CENTER SAS', 'David Caro Morales cerró la oportunidad de \"GRU & TAX CENTER SAS\" como GANADA.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 14:42:01'),
(9, 1, 'venta_ganada', 'Venta registrada: GRU & TAX CENTER SAS', 'David Caro Morales registró una venta de $20,000,000.00 el 2026-02-24.', 'http://localhost/crm-php.com/index.php?controller=venta&action=index', 1, '2026-02-24 14:42:18'),
(10, 4, 'venta_ganada', 'Venta registrada: GRU & TAX CENTER SAS', 'David Caro Morales registró una venta de $20,000,000.00 el 2026-02-24.', 'http://localhost/crm-php.com/index.php?controller=venta&action=index', 1, '2026-02-24 14:42:18'),
(11, 1, 'cambio_etapa', 'Cambio de etapa: GLOBAL CARGAS LOGISTICS', 'David Caro Morales movió \"GLOBAL CARGAS LOGISTICS\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 15:16:02'),
(12, 4, 'cambio_etapa', 'Cambio de etapa: GLOBAL CARGAS LOGISTICS', 'David Caro Morales movió \"GLOBAL CARGAS LOGISTICS\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 15:16:02'),
(13, 1, 'cambio_etapa', 'Cambio de etapa: GLOBAL CARGAS LOGISTICS', 'David Caro Morales movió \"GLOBAL CARGAS LOGISTICS\" de Contactado → Negociacion.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 15:17:17'),
(14, 4, 'cambio_etapa', 'Cambio de etapa: GLOBAL CARGAS LOGISTICS', 'David Caro Morales movió \"GLOBAL CARGAS LOGISTICS\" de Contactado → Negociacion.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 15:17:17'),
(15, 1, 'venta_ganada', 'Venta registrada: COMPRA Y VENTA MATERIAL DE SEGUNDA LAS DOS RR', 'David Caro Morales registró una venta de $20,000,000.00 el 2026-02-24.', 'http://localhost/crm-php.com/index.php?controller=venta&action=index', 1, '2026-02-24 15:17:55'),
(16, 4, 'venta_ganada', 'Venta registrada: COMPRA Y VENTA MATERIAL DE SEGUNDA LAS DOS RR', 'David Caro Morales registró una venta de $20,000,000.00 el 2026-02-24.', 'http://localhost/crm-php.com/index.php?controller=venta&action=index', 1, '2026-02-24 15:17:55'),
(17, 1, 'cambio_etapa', 'Cambio de etapa: AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'David Caro Morales movió \"AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA\" de Prospectado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 16:29:44'),
(18, 4, 'cambio_etapa', 'Cambio de etapa: AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'David Caro Morales movió \"AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA\" de Prospectado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 16:29:44'),
(19, 1, 'cambio_etapa', 'Cambio de etapa: AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'David Caro Morales movió \"AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA\" de Perdido → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 17:03:38'),
(20, 4, 'cambio_etapa', 'Cambio de etapa: AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'David Caro Morales movió \"AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA\" de Perdido → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 17:03:38'),
(21, 1, 'cambio_etapa', 'Cambio de etapa: AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'David Caro Morales movió \"AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA\" de Contactado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 17:06:14'),
(22, 4, 'cambio_etapa', 'Cambio de etapa: AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'David Caro Morales movió \"AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA\" de Contactado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 17:06:14'),
(23, 1, 'cambio_etapa', 'Cambio de etapa: CIA MELGAR S.A.S', 'David Caro Morales movió \"CIA MELGAR S.A.S\" de Contactado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 20:10:00'),
(24, 4, 'cambio_etapa', 'Cambio de etapa: CIA MELGAR S.A.S', 'David Caro Morales movió \"CIA MELGAR S.A.S\" de Contactado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 20:10:00'),
(25, 1, 'cambio_etapa', 'Cambio de etapa: CENTRAL LOGISTICA DE TRANSPORTE FRISON S.A.S', 'David Caro Morales movió \"CENTRAL LOGISTICA DE TRANSPORTE FRISON S.A.S\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 20:49:38'),
(26, 4, 'cambio_etapa', 'Cambio de etapa: CENTRAL LOGISTICA DE TRANSPORTE FRISON S.A.S', 'David Caro Morales movió \"CENTRAL LOGISTICA DE TRANSPORTE FRISON S.A.S\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 20:49:38'),
(27, 1, 'cambio_etapa', 'Cambio de etapa: DISTRIBUIDORES DE HIELO', 'David Caro Morales movió \"DISTRIBUIDORES DE HIELO\" de Prospectado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 20:53:46'),
(28, 4, 'cambio_etapa', 'Cambio de etapa: DISTRIBUIDORES DE HIELO', 'David Caro Morales movió \"DISTRIBUIDORES DE HIELO\" de Prospectado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 20:53:46'),
(29, 1, 'cambio_etapa', 'Cambio de etapa: DISTRIBUIDORA MICHELLY JM', 'David Caro Morales movió \"DISTRIBUIDORA MICHELLY JM\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 20:54:27'),
(30, 4, 'cambio_etapa', 'Cambio de etapa: DISTRIBUIDORA MICHELLY JM', 'David Caro Morales movió \"DISTRIBUIDORA MICHELLY JM\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 20:54:27'),
(31, 1, 'cambio_etapa', 'Cambio de etapa: GLOBAL CARGAS LOGISTICS', 'David Caro Morales movió \"GLOBAL CARGAS LOGISTICS\" de Negociacion → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 21:05:10'),
(32, 4, 'cambio_etapa', 'Cambio de etapa: GLOBAL CARGAS LOGISTICS', 'David Caro Morales movió \"GLOBAL CARGAS LOGISTICS\" de Negociacion → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 21:05:10'),
(33, 1, 'cambio_etapa', 'Cambio de etapa: EL PIZARRON', 'David Caro Morales movió \"EL PIZARRON\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 21:14:04'),
(34, 4, 'cambio_etapa', 'Cambio de etapa: EL PIZARRON', 'David Caro Morales movió \"EL PIZARRON\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 21:14:04'),
(35, 1, 'cambio_etapa', 'Cambio de etapa: ELITE LOGISTICA SAN JOSE DEL GUAVIARE', 'David Caro Morales movió \"ELITE LOGISTICA SAN JOSE DEL GUAVIARE\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 21:28:18'),
(36, 4, 'cambio_etapa', 'Cambio de etapa: ELITE LOGISTICA SAN JOSE DEL GUAVIARE', 'David Caro Morales movió \"ELITE LOGISTICA SAN JOSE DEL GUAVIARE\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 21:28:18'),
(37, 1, 'cambio_etapa', 'Cambio de etapa: GRU & TAX CENTER SAS', 'David Caro Morales movió \"GRU & TAX CENTER SAS\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 21:30:52'),
(38, 4, 'cambio_etapa', 'Cambio de etapa: GRU & TAX CENTER SAS', 'David Caro Morales movió \"GRU & TAX CENTER SAS\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 21:30:52'),
(39, 1, 'cambio_etapa', 'Cambio de etapa: GRUAS Y CAMIONES SANABRIA', 'David Caro Morales movió \"GRUAS Y CAMIONES SANABRIA\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 22:46:17'),
(40, 4, 'cambio_etapa', 'Cambio de etapa: GRUAS Y CAMIONES SANABRIA', 'David Caro Morales movió \"GRUAS Y CAMIONES SANABRIA\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 22:46:17'),
(41, 1, 'cambio_etapa', 'Cambio de etapa: MAUH STORE', 'David Caro Morales movió \"MAUH STORE\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 22:47:10'),
(42, 4, 'cambio_etapa', 'Cambio de etapa: MAUH STORE', 'David Caro Morales movió \"MAUH STORE\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 22:47:10'),
(43, 1, 'cambio_etapa', 'Cambio de etapa: MUDANZAS REYES', 'David Caro Morales movió \"MUDANZAS REYES\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-27 22:48:43'),
(44, 4, 'cambio_etapa', 'Cambio de etapa: MUDANZAS REYES', 'David Caro Morales movió \"MUDANZAS REYES\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 0, '2026-03-27 22:48:43'),
(45, 1, 'cambio_etapa', 'Cambio de etapa: PARQUEADERO GRANCOLOMBIANA', 'David Caro Morales movió \"PARQUEADERO GRANCOLOMBIANA\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-28 09:46:18'),
(46, 1, 'cambio_etapa', 'Cambio de etapa: TRANSLIPER LIMITADA', 'David Caro Morales movió \"TRANSLIPER LIMITADA\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-28 10:27:33'),
(47, 1, 'cambio_etapa', 'Cambio de etapa: TRANSLIPER LIMITADA', 'David Caro Morales movió \"TRANSLIPER LIMITADA\" de Contactado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-28 10:30:18'),
(48, 1, 'cambio_etapa', 'Cambio de etapa: SCHAEFER CHARTERING COLOMBIA S.A.S.', 'David Caro Morales movió \"SCHAEFER CHARTERING COLOMBIA S.A.S.\" de Prospectado → Contactado.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-03-28 10:30:52'),
(49, 1, 'venta_ganada', 'Venta registrada: PARQUEADERO TEUSACA', 'David Caro Morales registró una venta de $12,000,000.00 el 2026-03-29.', 'http://localhost/crm-php.com/index.php?controller=venta&action=index', 1, '2026-03-29 18:03:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_preferencias`
--

CREATE TABLE `notificacion_preferencias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'NULL = preferencia global o por rol',
  `rol` varchar(50) DEFAULT NULL COMMENT 'Si usuario_id es NULL, aplica a este rol',
  `evento` varchar(50) NOT NULL COMMENT 'venta_ganada, empresa_creada, cambio_etapa, credito_aprobado',
  `canal_email` tinyint(1) DEFAULT 1,
  `canal_inapp` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificacion_preferencias`
--

INSERT INTO `notificacion_preferencias` (`id`, `usuario_id`, `rol`, `evento`, `canal_email`, `canal_inapp`) VALUES
(1, NULL, 'admin', 'venta_ganada', 1, 1),
(2, NULL, 'admin', 'empresa_creada', 0, 1),
(3, NULL, 'admin', 'cambio_etapa', 0, 1),
(4, NULL, 'admin', 'credito_aprobado', 1, 1),
(5, NULL, 'superadmin', 'venta_ganada', 1, 1),
(6, NULL, 'superadmin', 'empresa_creada', 1, 1),
(7, NULL, 'superadmin', 'cambio_etapa', 1, 1),
(8, NULL, 'superadmin', 'credito_aprobado', 1, 1),
(9, NULL, 'usuario', 'venta_ganada', 0, 1),
(10, NULL, 'usuario', 'empresa_creada', 0, 1),
(11, NULL, 'usuario', 'cambio_etapa', 0, 1),
(12, NULL, 'usuario', 'credito_aprobado', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trazabilidad`
--

CREATE TABLE `trazabilidad` (
  `id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `etapa_venta` enum('prospectado','contactado','negociacion','seguimiento','ganado','perdido') DEFAULT NULL,
  `tipo_actividad` enum('llamada','correo','reunion','visita','nota','Estudio de necesidades','Oferta de servicios','Seguimiento de la Oferta') NOT NULL DEFAULT 'nota',
  `fecha` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trazabilidad`
--

INSERT INTO `trazabilidad` (`id`, `empresa_id`, `usuario_id`, `etapa_venta`, `tipo_actividad`, `fecha`, `observaciones`) VALUES
(3, 31, 1, 'perdido', 'llamada', '2026-02-24 15:15:38', 'jfjfjgfjfgjfg'),
(5, 31, 1, 'prospectado', 'correo', '2026-02-24 19:26:48', ''),
(26, 38, 1, 'contactado', 'nota', '2026-03-27 21:14:04', 'Actualizacion comercial de empresa.\n- Cambio de etapa: Prospectado -> Contactado\n- Cambio de aplica: N/A -> SI\n- Observacion comercial: Empresa solicitó le envíen la oferta al correo y reunirse de forma virtual para conocer más'),
(27, 38, 1, 'contactado', 'Estudio de necesidades', '2026-03-27 21:14:55', 'Se evalua la mejor oferta para el cliente, ajuste de precios'),
(28, 38, 1, 'negociacion', 'Oferta de servicios', '2026-03-27 21:22:47', 'Se envía oferta de servicios a la empresa. '),
(29, 47, 1, 'contactado', 'nota', '2026-03-27 21:28:18', 'Actualizacion comercial de empresa.\n- Cambio de etapa: Prospectado -> Contactado\n- Cambio de aplica: N/A -> SI\n- Observacion comercial: Se realizó contacto con David Caro, el contador de la empresa. Solicitó conocer más a detalle la oferta de servicios'),
(32, 47, 1, 'contactado', 'Estudio de necesidades', '2026-03-27 21:32:01', 'Se ajusta oferta'),
(35, 39, 1, 'contactado', 'nota', '2026-03-27 22:48:43', 'Actualizacion comercial de empresa.\n- Cambio de etapa: Prospectado -> Contactado\n- Cambio de aplica: N/A -> SI'),
(37, 48, 1, 'ganado', 'reunion', '2026-03-28 09:46:38', ''),
(38, 43, 1, 'contactado', 'nota', '2026-03-28 10:27:33', 'Actualizacion comercial de empresa.\n- Cambio de etapa: Prospectado -> Contactado\n- Cambio de aplica: N/A -> SI'),
(39, 43, 1, 'perdido', 'nota', '2026-03-28 10:30:18', 'Actualizacion comercial de empresa.\n- Cambio de etapa: Contactado -> Perdido\n- Cambio de aplica: SI -> NO'),
(40, 31, 1, 'contactado', 'nota', '2026-03-28 10:30:52', 'Actualizacion comercial de empresa.\n- Cambio de etapa: Prospectado -> Contactado\n- Cambio de aplica: NO -> SI\n- Observacion comercial: --- Info Importada ---\r\nFuente Tel: GOOGLE.'),
(41, 38, 1, 'seguimiento', 'Seguimiento de la Oferta', '2026-03-28 10:52:12', 'La empresa solicitó una reunión presencial para ajustar los términos del contrato.'),
(42, 39, 1, 'negociacion', 'Oferta de servicios', '2026-03-28 11:17:02', ''),
(46, 38, 1, 'perdido', 'nota', '2026-03-28 16:23:06', 'Cliente decidió no continuar\r\n'),
(47, 39, 1, 'seguimiento', 'Seguimiento de la Oferta', '2026-03-28 16:35:14', ''),
(49, 31, 1, 'contactado', 'Estudio de necesidades', '2026-03-28 16:36:41', ''),
(50, 41, 1, 'negociacion', 'Oferta de servicios', '2026-03-28 16:42:23', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `primer_login` tinyint(1) DEFAULT 1,
  `ultimo_cambio_password` datetime DEFAULT NULL,
  `rol` enum('usuario','admin','superadmin') DEFAULT 'usuario',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `creado_en` datetime DEFAULT current_timestamp(),
  `recovery_token` varchar(100) DEFAULT NULL,
  `recovery_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `primer_login`, `ultimo_cambio_password`, `rol`, `estado`, `creado_en`, `recovery_token`, `recovery_expira`) VALUES
(1, 'David Caro Morales', 'liderestrategiadigital@bahariaqua.com', '$2y$10$RcB86FMaHFkawyC4RYjTg.S8gWisxsDCovGQcJOtCcltQ/EqP6wmW', 0, '2026-02-24 09:53:29', 'superadmin', 'activo', '2026-02-23 10:55:10', '3ed762d8a5ab10d8d462176ee4a40fe306bc2a8309061588d35e55b36260b9e5', '2026-02-25 07:58:58'),
(4, 'Yorleidys Ruiz', 'comercial@bahariaqua.com', '$2y$10$XOIlqK6qx27yHm6TBivlIuYwMQ8FAQktr/olFz1kztCF/pe98ozT2', 1, '2026-03-27 22:12:28', 'usuario', 'activo', '2026-02-24 08:05:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha` date NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `empresa_id`, `monto`, `fecha`, `usuario_id`, `creado_en`) VALUES
(5, 48, 12000000.00, '2026-03-29', 1, '2026-03-29 18:03:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_clientes`
--

CREATE TABLE `ventas_clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_cliente` bigint(20) UNSIGNED NOT NULL,
  `monto` decimal(9,2) NOT NULL,
  `fecha` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas_clientes`
--

INSERT INTO `ventas_clientes` (`id`, `id_cliente`, `monto`, `fecha`) VALUES
(1, 1, 12000.00, '2026-02-23');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `integraciones`
--
ALTER TABLE `integraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_leida` (`usuario_id`,`leida`),
  ADD KEY `idx_creado` (`creado_en`);

--
-- Indices de la tabla `notificacion_preferencias`
--
ALTER TABLE `notificacion_preferencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pref_usuario_evento` (`usuario_id`,`evento`),
  ADD UNIQUE KEY `uq_pref_rol_evento` (`rol`,`evento`),
  ADD KEY `idx_evento` (`evento`);

--
-- Indices de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_venta_empresa` (`empresa_id`),
  ADD KEY `fk_venta_usuario` (`usuario_id`);

--
-- Indices de la tabla `ventas_clientes`
--
ALTER TABLE `ventas_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `integraciones`
--
ALTER TABLE `integraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `notificacion_preferencias`
--
ALTER TABLE `notificacion_preferencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas_clientes`
--
ALTER TABLE `ventas_clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`);

--
-- Filtros para la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `empresas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificacion_preferencias`
--
ALTER TABLE `notificacion_preferencias`
  ADD CONSTRAINT `notificacion_preferencias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  ADD CONSTRAINT `trazabilidad_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `trazabilidad_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_venta_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_venta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas_clientes`
--
ALTER TABLE `ventas_clientes`
  ADD CONSTRAINT `ventas_clientes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
