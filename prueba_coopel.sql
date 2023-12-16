-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-12-2023 a las 17:56:51
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prueba_coopel`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `calcularSueldoPorId` (IN `trabajadorId` INT, IN `mes` INT, IN `ano` INT)   BEGIN
    DECLARE salarioPorHora INT;
    DECLARE bonoXHora INT;
    DECLARE entregasXFecha DECIMAL(10,2);
    DECLARE totalSalarioBase DECIMAL(10,2);
    DECLARE totalBono DECIMAL(10,2);
    DECLARE totalEntregas DECIMAL(10,2);
    DECLARE sueldoBruto DECIMAL(10,2);
    DECLARE sueldoNeto DECIMAL(10,2);
    DECLARE totalValesDespensa DECIMAL(10,2);
    DECLARE salarioFinal DECIMAL(10,2);
    DECLARE valesDespensaPorcentaje DECIMAL(10,2);


    -- Obtener datos del trabajador sueldo por hora
    SELECT sueldoPorHora INTO salarioPorHora
    FROM Trabajadores
    WHERE idTrabajador = trabajadorId;
    
     -- Obtener datos del trabajador bono
    SELECT bonoPorHora INTO bonoXHora
    FROM Trabajadores
    WHERE idTrabajador = trabajadorId;
    
    SET entregasXFecha = 0;

    
    -- Obtener datos del trabajador entregas
	SELECT IFNULL( SUM(cantidadEntregas),0) AS totalEntregas INTO entregasXFecha
	FROM entregas
	WHERE YEAR(entregas.fecha) = ano 
  	AND MONTH(entregas.fecha) = mes
  	AND idTrabajador = trabajadorId;
    
     -- Obtener datos del trabajador vales de despensa
    SELECT valesDespensa INTO valesDespensaPorcentaje
    FROM Trabajadores
    WHERE idTrabajador = trabajadorId;




    -- Calcular los valores
    SET totalSalarioBase = (salarioPorHora*8*6*4) ;
    SET totalBono = (bonoXHora*8*6*4); 
    SET totalEntregas = entregasXFecha * 5; 
    SET sueldoBruto = totalSalarioBase + totalBono + totalEntregas;
    -- Aplicar descuento condicional
    IF sueldoBruto > 10000 THEN
        SET sueldoNeto = sueldoBruto - (sueldoBruto * 0.12);
    ELSE
        SET sueldoNeto = sueldoBruto - (sueldoBruto * 0.09);
    END IF;
    SET totalValesDespensa = (sueldoBruto * valesDespensaPorcentaje) ; -- Reemplaza con tu lógica real
    SET salarioFinal = sueldoNeto + totalValesDespensa; -- Reemplaza con tu lógica real

    -- Insertar los resultados en la tabla Sueldos
    INSERT INTO Sueldos (idTrabajador, totalSalarioBase, totalBono, totalEntregas, sueldoBruto, sueldoNeto, totalValesDespensa, SalarioFinaldecimal,mesSalario,año)
    VALUES (trabajadorId, totalSalarioBase, totalBono, totalEntregas, sueldoBruto, sueldoNeto, totalValesDespensa, salarioFinal,mes,ano);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `editarTrabajador` (IN `e_idTrabajador` INT, IN `e_nombreCompleto` VARCHAR(255), IN `e_idRol` INT, IN `e_numeroEmpleado` VARCHAR(255))   BEGIN
    UPDATE trabajadores
    SET
        nombreCompleto = e_nombreCompleto,
        idRol = e_idRol,
        numeroEmpleado = e_numeroEmpleado
        
    WHERE
        idTrabajador = e_idTrabajador;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `eliminarSueldoPorID` (IN `d_idSueldo` INT)   BEGIN
    DELETE FROM sueldos
    WHERE
        idSueldo = d_idSueldo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarEntrega` (IN `idTrabajador` INT, IN `p_precioEntrega` INT, IN `p_cantidadEntregas` INT, IN `p_fecha` DATE)   BEGIN
    INSERT INTO Entregas (idEntrega,idTrabajador, precioEntrega, cantidadEntregas, fecha)
    VALUES (null,idTrabajador, p_precioEntrega, p_cantidadEntregas, p_fecha);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarTrabajador` (IN `I_nombreCompleto` VARCHAR(255), IN `I_idRol` INT, IN `I_numeroEmpleado` VARCHAR(255), IN `I_bonoPorHora` DECIMAL(10,2), IN `I_sueldoPorHora` DECIMAL(10,2), IN `I_valesDespensa` DECIMAL(10,2))   BEGIN
    INSERT INTO `trabajadores`(`idTrabajador`, `nombreCompleto`, `idRol`, `numeroEmpleado`, `bonoPorHora`, `sueldoPorHora`, `valesDespensa`)
    VALUES (NULL, I_nombreCompleto, I_idRol, I_numeroEmpleado,I_bonoPorHora,I_sueldoPorHora,I_valesDespensa);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `idEntrega` int(11) NOT NULL,
  `idTrabajador` int(11) DEFAULT NULL,
  `precioEntrega` int(11) DEFAULT NULL,
  `cantidadEntregas` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entregas`
--

INSERT INTO `entregas` (`idEntrega`, `idTrabajador`, `precioEntrega`, `cantidadEntregas`, `fecha`) VALUES
(1, 1, 5, 10, '2023-12-16'),
(2, 2, 5, 6, '2023-12-15'),
(4, 1, 5, 9, '2023-12-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `rol`) VALUES
(1, 'choferes'),
(2, 'cargadores'),
(3, 'auxiliares');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sueldos`
--

CREATE TABLE `sueldos` (
  `idSueldo` int(11) NOT NULL,
  `idTrabajador` int(11) DEFAULT NULL,
  `totalSalarioBase` decimal(10,2) DEFAULT NULL,
  `totalBono` decimal(10,2) DEFAULT NULL,
  `totalEntregas` decimal(10,2) DEFAULT NULL,
  `sueldoBruto` decimal(10,2) DEFAULT NULL,
  `sueldoNeto` decimal(10,2) DEFAULT NULL,
  `totalValesDespensa` decimal(10,2) DEFAULT NULL,
  `SalarioFinaldecimal` decimal(10,2) DEFAULT NULL,
  `mesSalario` varchar(255) DEFAULT NULL,
  `año` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sueldos`
--

INSERT INTO `sueldos` (`idSueldo`, `idTrabajador`, `totalSalarioBase`, `totalBono`, `totalEntregas`, `sueldoBruto`, `sueldoNeto`, `totalValesDespensa`, `SalarioFinaldecimal`, `mesSalario`, `año`) VALUES
(4, 1, '5760.00', '1920.00', '95.00', '7775.00', '7075.25', '311.00', '7386.25', '12', '2023');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `idTrabajador` int(11) NOT NULL,
  `nombreCompleto` varchar(250) DEFAULT NULL,
  `idRol` int(11) DEFAULT NULL,
  `numeroEmpleado` varchar(100) DEFAULT NULL,
  `bonoPorHora` int(11) DEFAULT NULL,
  `sueldoPorHora` int(11) DEFAULT NULL,
  `valesDespensa` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`idTrabajador`, `nombreCompleto`, `idRol`, `numeroEmpleado`, `bonoPorHora`, `sueldoPorHora`, `valesDespensa`) VALUES
(1, 'Andres Gustavo Diaz', 1, 'R001', 10, 30, '0.04'),
(2, 'Elvis Andre Rodriguez', 2, 'R002', 5, 30, '0.04'),
(3, 'Ashley Julieth Diaz', 3, 'R003', 0, 30, '0.04'),
(4, 'Franklin Diaz', 1, 'R004', 10, 30, '0.04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`idEntrega`),
  ADD KEY `idTrabajador` (`idTrabajador`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `sueldos`
--
ALTER TABLE `sueldos`
  ADD PRIMARY KEY (`idSueldo`),
  ADD KEY `idTrabajador` (`idTrabajador`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`idTrabajador`),
  ADD UNIQUE KEY `numeroEmpleado` (`numeroEmpleado`),
  ADD KEY `idRol` (`idRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `idEntrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sueldos`
--
ALTER TABLE `sueldos`
  MODIFY `idSueldo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `idTrabajador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `entregas_ibfk_1` FOREIGN KEY (`idTrabajador`) REFERENCES `trabajadores` (`idTrabajador`);

--
-- Filtros para la tabla `sueldos`
--
ALTER TABLE `sueldos`
  ADD CONSTRAINT `sueldos_ibfk_1` FOREIGN KEY (`idTrabajador`) REFERENCES `trabajadores` (`idTrabajador`);

--
-- Filtros para la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD CONSTRAINT `trabajadores_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
