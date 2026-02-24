-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-02-2026 a las 17:17:41
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
  `etapa_venta` enum('prospectado','contactado','negociacion','ganado','perdido') DEFAULT 'prospectado',
  `observaciones` text DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `razon_social`, `dpto`, `ciudad`, `actividad_economica`, `correo_comercial`, `aplica`, `etapa_venta`, `observaciones`, `usuario_id`, `creado_en`) VALUES
(2, 'SCHAEFER CHARTERING COLOMBIA S.A.S.', 'ATLANTICO', 'BARRANQUILLA', 'OTRAS ACTIV COMP/TRANSP', 'accounting@schaefer-bma.com', 'NO', 'prospectado', '--- Info Importada ---\r\nFuente Tel: GOOGLE.', 1, '2026-02-23 16:44:11'),
(3, 'COMPRA Y VENTA MATERIAL DE SEGUNDA LAS DOS RR', 'CUNDINAMARCA', 'FUNZA', 'TRANSP CARGA/CARRETERA', 'rogelio.robayo56@gmail.com', 'SI', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(4, 'GLOBAL CARGAS LOGISTICS', 'CESAR', 'VALLEDUPAR', 'TRANSP CARGA/CARRETERA', 'globalcargaslogistics@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(5, 'GRU & TAX CENTER SAS', 'CAUCA', 'POPAYAN', 'TRANSP CARGA/CARRETERA', 'pdlao660924@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(6, 'CLC CARGAS Y LOGISTICAS DEL CARIBE', 'CESAR', 'BOSCONIA', 'TRANSP CARGA/CARRETERA', 'clc.cargasylogisticas@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(7, 'TRANSPORTES PEREZ PUERTO', 'ATLANTICO', 'SOLEDAD', 'TRANSP CARGA/CARRETERA', 'rh.gloriapuerto@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(8, 'EL PIZARRON', 'SANTANDER', 'BUCARAMANGA', 'TRANSP CARGA/CARRETERA', 'nandrearr@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(9, 'EL PIZARRON', 'SANTANDER', 'BUCARAMANGA', 'TRANSP CARGA/CARRETERA', 'nandrearr@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(10, 'MUDANZAS REYES', 'BOGOTA', 'BOGOTA 1', 'TRANSP CARGA/CARRETERA', 'mudanzasreyes@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(11, 'GRUAS Y CAMIONES SANABRIA', 'NORTE DE SANTANDER', 'CUCUTA', 'TRANSP CARGA/CARRETERA', 'yarleycano81@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(12, 'TRANSPORTE IG', 'VALLE DEL CAUCA', 'CALI', 'TRANSP CARGA/CARRETERA', 'ingridcali16@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(13, 'DISTRIBUIDORES DE HIELO', 'BOGOTA', 'BOGOTA 1', 'TRANSP CARGA/CARRETERA', 'distribuidoresdehieloyagua66@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(14, 'TRANSLIPER LIMITADA', 'ANTIOQUIA', 'MEDELLIN', 'TRANSP CARGA/CARRETERA', 'cenatac_ltda@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(15, 'DISTRIBUIDORA MICHELLY JM', 'VALLE DEL CAUCA', 'CALI', 'TRANSP CARGA/CARRETERA', 'brayanguapacho03@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(16, 'TRANSPORTES & SOLUCIONES LOGISTICAS TRANSLOG S.A.S.', 'ATLANTICO', 'BARRANQUILLA', 'TRANSP CARGA/CARRETERA', 'transologsas@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(17, 'TURSENLUV', 'BOGOTA', 'BOGOTA 1', 'TRANSP CARGA/CARRETERA', 'tursenluv@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(18, 'ELITE LOGISTICA SAN JOSE DEL GUAVIARE', 'GUAVIARE', 'SAN JOSE DEL GUAVIARE', 'TRANSP CARGA/CARRETERA', 'contabilidad2@elitelogistica.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(19, 'PARQUEADERO TEUSACA', 'CUNDINAMARCA', 'LA CALERA', 'TRANSP CARGA/CARRETERA', 'wirima@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(20, 'TRANSPORTE MAYLI', 'TOLIMA', 'IBAGUE', 'TRANSP CARGA/CARRETERA', 'maritos2609@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(21, 'TRANSPORTES INTEGRALES DE ORIENTE SAS - SAN MARCOS', 'SUCRE', 'SAN MARCOS', 'TRANSP CARGA/CARRETERA', 'transportestio@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(22, 'AGENCIA SUPER CARGO LOGISTIC S.A.S BUENAVENTURA', 'VALLE DEL CAUCA', 'BUENAVENTURA', 'TRANSP CARGA/CARRETERA', 'gerencia@supercargosas.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(23, 'PARQUEADERO GRANCOLOMBIANA', 'BOGOTA', 'BOGOTA 1', 'TRANSP CARGA/CARRETERA', 'caalsanal@hotmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(24, 'CIA MELGAR S.A.S', 'TOLIMA', 'MELGAR', 'OTRAS ACTIV COMP/TRANSP', 'loyda.fajardo@incorporando.com.co', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(25, 'CIA MEGATRAM 2.0', 'ANTIOQUIA', 'ITAGUI', 'OTRAS ACTIV COMP/TRANSP', 'ciamegatram2@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(26, 'MAUH STORE', 'ANTIOQUIA', 'LA ESTRELLA', 'OTRAS ACTIV COMP/TRANSP', 'gerencia@mauhiff.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(27, 'AGENCIA DE ADUANAS JUNIOR ADUANAS S A NIVEL 2', 'BOGOTA', 'BOGOTA 1', 'OTRAS ACTIV COMP/TRANSP', 'bogota@junioraduanas.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(28, 'CENTRAL LOGISTICA DE TRANSPORTE FRISON S.A.S', 'VALLE DEL CAUCA', 'CALI', 'TRANSP CARGA/CARRETERA', 'cltfrison@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(29, 'TRASTEOS Y TRANSPORTES DIOMAR', 'VALLE DEL CAUCA', 'CALI', 'TRANSP CARGA/CARRETERA', 'rodrigosanchez198@gmail.com', '', 'prospectado', '', 1, '2026-02-23 16:44:11'),
(30, 'ALMAVIVA GLOBAL CARGO CH?A', 'CUNDINAMARCA', 'CHIA', 'TRANSP CARGA/CARRETERA', 'NOTIFICACIONES@ALMAVIVA.COM.CO', '', 'prospectado', '', 1, '2026-02-23 16:44:11');

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
(1, 1, 'cambio_etapa', 'Cambio de etapa: EMPRESA ABC', 'Superadmin movió \"EMPRESA ABC\" de Ganado → Perdido.', 'http://localhost/crm-php.com/index.php?controller=empresa&action=index', 1, '2026-02-24 08:56:24');

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
  `etapa_venta` enum('prospectado','contactado','negociacion','ganado','perdido') DEFAULT NULL,
  `tipo_actividad` enum('llamada','correo','reunion','visita','nota') NOT NULL DEFAULT 'nota',
  `fecha` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Superadmin', 'liderestrategiadigital@bahariaqua.com', '$2y$10$PxUE9oI4mIZxUHLNMm70SumO0BxiV3G9dJO4MEtsNuM.A80xw63P.', 0, '2026-02-24 09:53:29', 'superadmin', 'activo', '2026-02-23 10:55:10', '3ed762d8a5ab10d8d462176ee4a40fe306bc2a8309061588d35e55b36260b9e5', '2026-02-25 07:58:58'),
(4, 'Yorleidys Ruiz', 'comercial@bahariaqua.com', '$2y$10$zjQu5Bgj.bMtL/UYZd9AcuUxjl3eh.GdW6tOPK0GbqzttrrZgDvk6', 1, NULL, 'usuario', 'activo', '2026-02-24 08:05:03', NULL, NULL),
(7, 'David Caro', 'dacamo0502@gmail.com', '$2y$10$wGfxLvebLcoTim9p51GySuxO.NFHnPayMN80gEz8suQZa/B9/FNYi', 0, '2026-02-24 10:19:03', 'usuario', 'activo', '2026-02-24 09:56:03', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `integraciones`
--
ALTER TABLE `integraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificacion_preferencias`
--
ALTER TABLE `notificacion_preferencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
