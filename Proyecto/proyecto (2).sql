-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-06-2025 a las 13:30:42
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
-- Base de datos: `proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunidades`
--

CREATE TABLE `comunidades` (
  `id_comunidad` int(11) NOT NULL,
  `rif` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `ubicacion_estado` varchar(100) NOT NULL,
  `ubicacion_municipio` varchar(100) NOT NULL,
  `ubicacion_parroquia` varchar(100) NOT NULL,
  `ubicacion_calle_avenida` varchar(255) NOT NULL,
  `ubicacion_codigo_postal` varchar(10) DEFAULT NULL,
  `id_responsable_fk` int(11) NOT NULL,
  `correo_contacto` varchar(150) NOT NULL,
  `telefono_contacto` varchar(20) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id_docente` int(11) NOT NULL,
  `id_usuario_fk` int(11) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `especialidad` varchar(100) NOT NULL,
  `fecha_ingreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario_fk` int(11) NOT NULL,
  `carrera` varchar(100) NOT NULL,
  `semestre` int(11) NOT NULL,
  `fecha_matricula` date DEFAULT NULL,
  `id_proyecto_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante_materia`
--

CREATE TABLE `estudiante_materia` (
  `id_estudiante_fk` int(11) NOT NULL,
  `id_materia_fk` int(11) NOT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL,
  `estado_aprobacion` varchar(20) NOT NULL CHECK (`estado_aprobacion` in ('aprobado','reprobado','cursando','retirado')),
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `creditos` int(11) NOT NULL,
  `id_trayecto_fk` int(11) NOT NULL,
  `id_materia_prelacion_fk` int(11) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id_proyecto` int(11) NOT NULL,
  `numero_proyecto` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin_estimada` date DEFAULT NULL,
  `estado` varchar(50) NOT NULL CHECK (`estado` in ('activo','culminado','pendiente','cancelado')),
  `id_tutor_docente_fk` int(11) NOT NULL,
  `id_comunidad_fk` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trayectos`
--

CREATE TABLE `trayectos` (
  `id_trayecto` int(11) NOT NULL,
  `numero_trayecto` int(11) NOT NULL,
  `fase` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `cedula`, `nombre`, `apellido`, `correo`, `telefono`, `direccion`, `password`, `rol`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, '12345678', 'Admin', 'Maestro', 'admin@example.com', '04141234567', 'Av. Principal, Edificio Central', '$2y$10$92hD.u.eYxQZ.zYkK.n4c.d7N0.m.C0J.i.C0J.i.C0J.i.C0J.i.C0J.i.C0J.i', 'administrador', 'activo', '2025-06-14 17:10:22', '2025-06-14 17:10:22'),
(2, '9625928', 'Jose Luis ', 'Gonzalez', 'josibel7416@gmail.com', NULL, NULL, '$2y$10$hm9Z12IvZp/MGBxsYSyaK.9IgdUb5tmJ/vBdMe6yp3eJzEv/Dp.da', 'administrador', 'activo', '2025-06-14 18:03:31', '2025-06-14 18:03:31'),
(3, '18862633', 'Cristina', 'Capo', 'cristinacapo87@gmail.com', NULL, NULL, '$2y$10$KYyzPEmBKcylPJedloRu2.fBQVCsizdOHUHgUUs8hxoWrSrmPn1x2', 'estudiante', 'activo', '2025-06-16 20:00:26', '2025-06-16 20:00:26');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comunidades`
--
ALTER TABLE `comunidades`
  ADD PRIMARY KEY (`id_comunidad`),
  ADD UNIQUE KEY `rif` (`rif`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `correo_contacto` (`correo_contacto`),
  ADD KEY `fk_comunidad_responsable` (`id_responsable_fk`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id_docente`),
  ADD UNIQUE KEY `id_usuario_fk` (`id_usuario_fk`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD UNIQUE KEY `id_usuario_fk` (`id_usuario_fk`),
  ADD KEY `fk_estudiante_proyecto` (`id_proyecto_fk`);

--
-- Indices de la tabla `estudiante_materia`
--
ALTER TABLE `estudiante_materia`
  ADD PRIMARY KEY (`id_estudiante_fk`,`id_materia_fk`),
  ADD KEY `fk_estmat_materia` (`id_materia_fk`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_materia_trayecto` (`id_trayecto_fk`),
  ADD KEY `fk_materia_prelacion` (`id_materia_prelacion_fk`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id_proyecto`),
  ADD UNIQUE KEY `numero_proyecto` (`numero_proyecto`),
  ADD KEY `fk_proyecto_tutor` (`id_tutor_docente_fk`),
  ADD KEY `fk_proyecto_comunidad` (`id_comunidad_fk`);

--
-- Indices de la tabla `trayectos`
--
ALTER TABLE `trayectos`
  ADD PRIMARY KEY (`id_trayecto`),
  ADD UNIQUE KEY `numero_trayecto` (`numero_trayecto`,`fase`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comunidades`
--
ALTER TABLE `comunidades`
  MODIFY `id_comunidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trayectos`
--
ALTER TABLE `trayectos`
  MODIFY `id_trayecto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comunidades`
--
ALTER TABLE `comunidades`
  ADD CONSTRAINT `fk_comunidad_responsable` FOREIGN KEY (`id_responsable_fk`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `fk_docente_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_estudiante_proyecto` FOREIGN KEY (`id_proyecto_fk`) REFERENCES `proyectos` (`id_proyecto`),
  ADD CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `estudiante_materia`
--
ALTER TABLE `estudiante_materia`
  ADD CONSTRAINT `fk_estmat_estudiante` FOREIGN KEY (`id_estudiante_fk`) REFERENCES `estudiantes` (`id_estudiante`),
  ADD CONSTRAINT `fk_estmat_materia` FOREIGN KEY (`id_materia_fk`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `fk_materia_prelacion` FOREIGN KEY (`id_materia_prelacion_fk`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `fk_materia_trayecto` FOREIGN KEY (`id_trayecto_fk`) REFERENCES `trayectos` (`id_trayecto`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_proyecto_comunidad` FOREIGN KEY (`id_comunidad_fk`) REFERENCES `comunidades` (`id_comunidad`),
  ADD CONSTRAINT `fk_proyecto_tutor` FOREIGN KEY (`id_tutor_docente_fk`) REFERENCES `docentes` (`id_docente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
