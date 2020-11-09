-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2020 a las 14:35:39
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
-- Base de datos: comanda
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla empleados
--

CREATE TABLE empleados (
  id int(11) NOT NULL,
  id_persona int(11) NOT NULL,
  id_sector int(11) NOT NULL,
  id_puesto int(11) NOT NULL,
  id_estado int(11) NOT NULL,
  created_at date NOT NULL,
  updated_at date DEFAULT NULL,
  deleted_at date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla estado_empleados
--

CREATE TABLE estado_empleados (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla estado_empleados
--

INSERT INTO estado_empleados (id, descripcion) VALUES
(1, 'ACTIVO'),
(2, 'SUSPENDIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla estado_mesa
--

CREATE TABLE estado_mesa (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla estado_mesa
--

INSERT INTO estado_mesa (id, descripcion) VALUES
(1, 'con cliente esperando pedido'),
(2, 'con clientes comiendo'),
(3, 'con clientes pagando'),
(4, 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla estado_pedido
--

CREATE TABLE estado_pedido (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla estado_pedido
--

INSERT INTO estado_pedido (id, descripcion) VALUES
(1, 'PENDIENTE'),
(2, 'EN PREPARACION'),
(3, 'LISTO PARA SERVIR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla mesas
--

CREATE TABLE mesas (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL,
  codigo varchar(5) NOT NULL,
  id_estado int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla pedidos
--

CREATE TABLE pedidos (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL,
  id_sector int(11) NOT NULL,
  id_estado int(11) NOT NULL,
  codigo varchar(5) NOT NULL,
  id_empleado int(11) DEFAULT NULL,
  delivery_time datetime DEFAULT NULL,
  monto decimal(10,2) NOT NULL,
  photo varchar(100) DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime DEFAULT NULL,
  deleted_at datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla personas
--

CREATE TABLE personas (
  id int(11) NOT NULL,
  nombre varchar(30) NOT NULL,
  apellido varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla puestos
--

CREATE TABLE puestos (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla puestos
--

INSERT INTO puestos (id, descripcion) VALUES
(1, 'BARTENDER'),
(2, 'CERVECERO'),
(3, 'COCINERO'),
(4, 'MOZO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla sectors
--

CREATE TABLE sectors (
  id int(11) NOT NULL,
  descripcion varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla sectors
--

INSERT INTO sectors (id, descripcion) VALUES
(1, 'BARRA VINOS Y TRAGOS'),
(2, 'BARRA CHOPERA'),
(3, 'COCINA'),
(4, 'CANDY BAR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla socios
--

CREATE TABLE socios (
  id int(11) NOT NULL,
  id_persona int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla empleados
--
ALTER TABLE empleados
  ADD PRIMARY KEY (id),
  ADD KEY id_estado (id_estado),
  ADD KEY id_persona (id_persona),
  ADD KEY id_sector (id_sector),
  ADD KEY id_puesto (id_puesto);

--
-- Indices de la tabla estado_empleados
--
ALTER TABLE estado_empleados
  ADD PRIMARY KEY (id);

--
-- Indices de la tabla estado_mesa
--
ALTER TABLE estado_mesa
  ADD PRIMARY KEY (id);

--
-- Indices de la tabla estado_pedido
--
ALTER TABLE estado_pedido
  ADD PRIMARY KEY (id);

--
-- Indices de la tabla mesas
--
ALTER TABLE mesas
  ADD PRIMARY KEY (id),
  ADD KEY id_estado (id_estado);

--
-- Indices de la tabla pedidos
--
ALTER TABLE pedidos
  ADD PRIMARY KEY (id),
  ADD KEY id_estado (id_estado),
  ADD KEY id_sector (id_sector),
  ADD KEY id_empleado (id_empleado);

--
-- Indices de la tabla personas
--
ALTER TABLE personas
  ADD PRIMARY KEY (id);

--
-- Indices de la tabla puestos
--
ALTER TABLE puestos
  ADD PRIMARY KEY (id);

--
-- Indices de la tabla sectors
--
ALTER TABLE sectors
  ADD PRIMARY KEY (id);

--
-- Indices de la tabla socios
--
ALTER TABLE socios
  ADD PRIMARY KEY (id),
  ADD KEY id_persona (id_persona);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla estado_empleados
--
ALTER TABLE estado_empleados
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla estado_mesa
--
ALTER TABLE estado_mesa
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla estado_pedido
--
ALTER TABLE estado_pedido
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla mesas
--
ALTER TABLE mesas
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla pedidos
--
ALTER TABLE pedidos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla personas
--
ALTER TABLE personas
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla puestos
--
ALTER TABLE puestos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla sectors
--
ALTER TABLE sectors
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla socios
--
ALTER TABLE socios
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla empleados
--
ALTER TABLE empleados
  ADD CONSTRAINT empleados_ibfk_1 FOREIGN KEY (id_estado) REFERENCES estado_empleados (id),
  ADD CONSTRAINT empleados_ibfk_2 FOREIGN KEY (id_persona) REFERENCES personas (id),
  ADD CONSTRAINT empleados_ibfk_3 FOREIGN KEY (id_sector) REFERENCES sectors (id),
  ADD CONSTRAINT empleados_ibfk_4 FOREIGN KEY (id_puesto) REFERENCES puestos (id);

--
-- Filtros para la tabla mesas
--
ALTER TABLE mesas
  ADD CONSTRAINT mesas_ibfk_1 FOREIGN KEY (id_estado) REFERENCES estado_mesa (id);

--
-- Filtros para la tabla pedidos
--
ALTER TABLE pedidos
  ADD CONSTRAINT pedidos_ibfk_1 FOREIGN KEY (id_estado) REFERENCES estado_pedido (id),
  ADD CONSTRAINT pedidos_ibfk_2 FOREIGN KEY (id_sector) REFERENCES sectors (id),
  ADD CONSTRAINT pedidos_ibfk_3 FOREIGN KEY (id_empleado) REFERENCES empleados (id);

--
-- Filtros para la tabla socios
--
ALTER TABLE socios
  ADD CONSTRAINT socios_ibfk_1 FOREIGN KEY (id_persona) REFERENCES personas (id);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
