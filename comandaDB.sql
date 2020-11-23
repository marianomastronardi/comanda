-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2020 a las 20:35:31
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `persona_id`) VALUES
(1, 17),
(2, 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `puesto_id` int(11) NOT NULL,
  `estado_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `persona_id`, `sector_id`, `puesto_id`, `estado_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 11, 1, 1, 2, '2020-11-08 00:00:00', '2020-11-09 00:00:00', NULL),
(2, 12, 3, 3, 2, '2020-11-09 04:51:51', '2020-11-09 04:53:03', NULL),
(3, 13, 3, 3, 1, '2020-11-09 04:56:25', '2020-11-09 04:56:25', NULL),
(4, 1, 4, 4, 1, '2020-11-22 17:17:59', '2020-11-22 17:17:59', NULL),
(5, 18, 4, 4, 1, '2020-11-22 20:55:54', '2020-11-22 20:55:54', NULL),
(6, 19, 2, 2, 1, '2020-11-22 20:56:52', '2020-11-22 20:56:52', NULL),
(7, 20, 1, 1, 1, '2020-11-22 20:57:14', '2020-11-22 20:57:14', NULL),
(8, 21, 3, 3, 1, '2020-11-22 20:57:38', '2020-11-22 20:57:38', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_empleados`
--

CREATE TABLE `estado_empleados` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_empleados`
--

INSERT INTO `estado_empleados` (`id`, `descripcion`) VALUES
(1, 'ACTIVO'),
(2, 'SUSPENDIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_mesas`
--

CREATE TABLE `estado_mesas` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_mesas`
--

INSERT INTO `estado_mesas` (`id`, `descripcion`) VALUES
(1, 'con cliente esperando pedido'),
(2, 'con clientes comiendo'),
(3, 'con clientes pagando'),
(4, 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_pedidos`
--

CREATE TABLE `estado_pedidos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_pedidos`
--

INSERT INTO `estado_pedidos` (`id`, `descripcion`) VALUES
(1, 'PENDIENTE'),
(2, 'EN PREPARACION'),
(3, 'LISTO PARA SERVIR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `estado_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `monto` decimal(10,0) NOT NULL,
  `delivery_time` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `descripcion`, `id_estado`) VALUES
(1, 'Mesa 1', 4),
(2, 'Mesa 2', 4),
(3, 'Mesa 3', 4),
(4, 'Mesa 4', 1),
(5, 'Mesa 5', 4),
(6, 'Mesa 6', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `estado_id` int(11) NOT NULL,
  `mesa_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `delivery_time` datetime DEFAULT NULL,
  `photo` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id`, `nombre`, `apellido`, `email`) VALUES
(1, 'Mariano', 'Mastronardi', 'mmastronardi@gmail.com'),
(3, 'Mario', 'Rampi', 'mrampi@gmail.com'),
(5, 'Luis', 'Suarez', 'lsuarez@gmail.com'),
(6, 'Valentin', 'Garcia Mora', 'vgmora@gmail.com'),
(7, 'Mauricio', 'D\'avila', 'mdavila@gmail.com'),
(8, 'Federico', 'Davila', 'fdavila@gmail.com'),
(11, 'Juan', 'Perez', 'jperez@gmail.com'),
(12, 'Mariano', 'Arias', 'marias@gmail.com'),
(13, 'Pablo', 'Gorgal', 'pgorgal@gmail.com'),
(14, 'Marcos', 'Reina', 'mreina@gmail.com'),
(16, 'Enrique', 'Diaz', 'ediaz@gmail.com'),
(17, 'Gabriel', 'Ruckert', 'gruckert@gmail.com'),
(18, 'Mister', 'Mozo', 'mozo@gmail.com'),
(19, 'Mister', 'Chop', 'cervecero@gmail.com'),
(20, 'Mister', 'Wine', 'bartender@gmail.com'),
(21, 'Mister', 'Cook', 'cocinero@gmail.com'),
(22, 'Mr', 'Socio', 'socio@gmail.com'),
(23, 'Mr', 'Customer', 'cliente@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sector_id` int(11) NOT NULL,
  `precio` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `descripcion`, `sector_id`, `precio`) VALUES
(1, 'empanadas', 3, '42'),
(2, 'Vino  Don Valentin', 1, '450'),
(3, 'Chop', 2, '175'),
(4, 'Budin de Pan', 4, '120'),
(5, 'Flan', 4, '180'),
(6, 'Pizza Muzzarella', 3, '330');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos`
--

CREATE TABLE `puestos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `puestos`
--

INSERT INTO `puestos` (`id`, `descripcion`) VALUES
(1, 'BARTENDER'),
(2, 'CERVECERO'),
(3, 'COCINERO'),
(4, 'MOZO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectors`
--

CREATE TABLE `sectors` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sectors`
--

INSERT INTO `sectors` (`id`, `descripcion`) VALUES
(1, 'BARRA VINOS Y TRAGOS'),
(2, 'BARRA CHOPERA'),
(3, 'COCINA'),
(4, 'CANDY BAR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`id`, `persona_id`) VALUES
(1, 7),
(2, 8),
(3, 16),
(4, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`email`, `password`) VALUES
('admin@gmail.com', '$2y$10$dDPqk4eIxNQMGGGu42f57.MrsTg/3kx0TROwirRekt9rlBsMV7rWO'),
('bartender@gmail.com', '$2y$10$E.9wNDrqAewIGpgV6gBs3.X4fmCGpR1laLLwSgAB7ICGdWu3zcAoO'),
('cervecero@gmail.com', '$2y$10$6VpacuSjyz2UjHRUtolPcOyP1KM57v9OrJby.x/Tp954iDIIBDfgC'),
('cliente@gmail.com', '$2y$10$ER/hMokJYHwX6MNiLOvDluzlo/KD4FGTQrGnHD5rwo/wxLUphBnJi'),
('cocinero@gmail.com', '$2y$10$jhL7y1eSA0WB/42dejtX6uaDdHk1OoPNv67V0ezM7soj99te5vxru'),
('ediaz@gmail.com', '$2y$10$saRz0OCnvC2egauHMTETvuj1mU16Bip5SSFeMfQQL3AN6YB.bdQRG'),
('fdavila@gmail.com', '$2y$10$TkxgJORq856CmHz/iKIfWelMHksOtF8t4RUoXWwHwp0Tr5IIYGSiu'),
('gruckert@gmail.com', '$2y$10$QOikoyQ5D9333FWmrQfGiud1xcK6xgO3GkUfXdqx7PrgMhZYfP4xu'),
('jperez@gmail.com', '$2y$10$QvLPIo3.5mU4Aw2W6UHGVec/8tH1OEovBWY1PUhLYwCTAjIstLImi'),
('lsuarez@gmail.com', '$2y$10$gx2cfvAKbHfE7pPPujXVVuCWVY1zYqUzw2nHjUjqAkTzIy.UjJ6QO'),
('marias@gmail.com', '$2y$10$T3uSbEoYFpvL.lHoeElh3uc14h6zxxuTk5UsAk7VeNp5f52MQle4C'),
('mdavila@gmail.com', '$2y$10$p8eypTo6DSrxBIhUsxUlBOCmTV/.PVkSooRMI70G1n3agxNneNteG'),
('mmastronardi@gmail.com', '$2y$10$jSsNWiV2C9rAF2Xi7afYXOpp9MREFMGlWKkGWvF63bFWY2taLb8XS'),
('mozo@gmail.com', '$2y$10$H7wFEzMPk4oMKEKQ1P5SHep4cRmP.nOrhb9si4eBEgtb5VnrtC5rq'),
('mrampi@gmail.com', '$2y$10$JwpuyE3tXGmKR8P3CI4XseN8RW9ju4FVheCAnu020wcTlOZiqr6NK'),
('mreina@gmail.com', '$2y$10$ILcxPY2mW5if46qnBpfzR.qv/FXtlPokvsSdtLBW0JRulZueIz1jW'),
('pgorgal@gmail.com', '$2y$10$Bpnh04AQhPBNhsYw2/aI5.ebHqqBGuAvW5doPlKZryVqW.FMg//Ja'),
('socio@gmail.com', '$2y$10$j9.FyV0oAj2TjZT4uVk/x.t8tbNL1ny8CjMIerdvoIyG2HMej7b7G'),
('vgmora@gmail.com', '$2y$10$mehG.5GlCkBBFGeSdd4Y8.gxL3rPvzxFX5G4OYvHFeaXqaSPZRd9.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `persona_id` (`persona_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `persona_id` (`persona_id`),
  ADD KEY `estado_id` (`estado_id`),
  ADD KEY `puesto_id` (`puesto_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Indices de la tabla `estado_empleados`
--
ALTER TABLE `estado_empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado_mesas`
--
ALTER TABLE `estado_mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado_pedidos`
--
ALTER TABLE `estado_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `sector_id` (`sector_id`),
  ADD KEY `estado_id` (`estado_id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estado_id` (`estado_id`),
  ADD KEY `mesa_id` (`mesa_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Indices de la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sectors`
--
ALTER TABLE `sectors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `persona_id` (`persona_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `estado_empleados`
--
ALTER TABLE `estado_empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estado_mesas`
--
ALTER TABLE `estado_mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estado_pedidos`
--
ALTER TABLE `estado_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `puestos`
--
ALTER TABLE `puestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sectors`
--
ALTER TABLE `sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`estado_id`) REFERENCES `estado_empleados` (`id`),
  ADD CONSTRAINT `empleados_ibfk_3` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`),
  ADD CONSTRAINT `empleados_ibfk_4` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`);

--
-- Filtros para la tabla `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`),
  ADD CONSTRAINT `items_ibfk_3` FOREIGN KEY (`estado_id`) REFERENCES `estado_pedidos` (`id`),
  ADD CONSTRAINT `items_ibfk_4` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `items_ibfk_5` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`);

--
-- Filtros para la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD CONSTRAINT `mesas_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estado_mesas` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`estado_id`) REFERENCES `estado_pedidos` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_5` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_6` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `personas_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`);

--
-- Filtros para la tabla `socios`
--
ALTER TABLE `socios`
  ADD CONSTRAINT `socios_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
