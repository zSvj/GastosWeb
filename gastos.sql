-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-12-2024 a las 09:05:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gastos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generar_reporte_por_categoria` (IN `p_categoria_id` INT)   BEGIN
    SELECT t.id, t.monto, t.tipo, t.fecha, t.descripcion
    FROM transacciones t
    WHERE t.categoria_id = p_categoria_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_transaccion` (IN `p_monto` DECIMAL(10,2), IN `p_tipo` ENUM('ingreso','gasto'), IN `p_categoria_id` INT, IN `p_fecha` DATE, IN `p_descripcion` TEXT)   BEGIN
    INSERT INTO transacciones (monto, tipo, categoria_id, fecha, descripcion)
    VALUES (p_monto, p_tipo, p_categoria_id, p_fecha, p_descripcion);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Comida'),
(2, 'Transporte'),
(3, 'Salud'),
(4, 'Ocio'),
(5, 'Vivienda');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas`
--

CREATE TABLE `metas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `objetivo` decimal(10,2) NOT NULL,
  `ahorro_actual` decimal(10,2) DEFAULT 0.00,
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('en progreso','completada') DEFAULT 'en progreso',
  `descripcion` varchar(255) NOT NULL,
  `ahorro_objetivo` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metas`
--

INSERT INTO `metas` (`id`, `nombre`, `objetivo`, `ahorro_actual`, `fecha_limite`, `estado`, `descripcion`, `ahorro_objetivo`) VALUES
(78, 'Casa', 0.00, 1300000.00, NULL, 'en progreso', '', 2000000.00);

--
-- Disparadores `metas`
--
DELIMITER $$
CREATE TRIGGER `log_goal_changes` AFTER UPDATE ON `metas` FOR EACH ROW BEGIN
    -- Inserta un registro en metas_log cuando haya un cambio en las columnas clave
    IF OLD.objetivo != NEW.objetivo OR OLD.ahorro_actual != NEW.ahorro_actual OR OLD.estado != NEW.estado THEN
        INSERT INTO metas_log (meta_id, old_value, new_value, change_date)
        VALUES 
            (OLD.id, 
             CONCAT('Objetivo: ', OLD.objetivo, ', Ahorro Actual: ', OLD.ahorro_actual, ', Estado: ', OLD.estado), 
             CONCAT('Objetivo: ', NEW.objetivo, ', Ahorro Actual: ', NEW.ahorro_actual, ', Estado: ', NEW.estado),
             NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_gastos`
--

CREATE TABLE `registro_gastos` (
  `id` int(11) NOT NULL,
  `gasto_id` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL,
  `tipo` enum('ingreso','gasto') NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transacciones`
--

INSERT INTO `transacciones` (`id`, `tipo`, `categoria`, `monto`, `descripcion`, `fecha`) VALUES
(14, 'gasto', '1', 3333.00, '1', '2024-12-20 05:00:00');

--
-- Disparadores `transacciones`
--
DELIMITER $$
CREATE TRIGGER `budget_exceeded_alert` AFTER INSERT ON `transacciones` FOR EACH ROW BEGIN
    DECLARE total DECIMAL(10,2);

    -- Calcula el total de gastos en la categoría de la transacción
    SELECT SUM(monto) INTO total
    FROM transacciones
    WHERE categoria = NEW.categoria;

    -- Verifica si el total excede el presupuesto definido
    IF total > (SELECT presupuesto FROM categorias WHERE id = NEW.categoria) THEN
        INSERT INTO alertas (categoria_id, mensaje, fecha)
        VALUES (NEW.categoria, 'Presupuesto excedido', NOW());
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_balance_after_transaction` AFTER INSERT ON `transacciones` FOR EACH ROW BEGIN
    -- Actualiza el saldo de la cuenta dependiendo del tipo de transacción
    IF NEW.tipo = 'ingreso' THEN
        UPDATE cuentas
        SET saldo = saldo + NEW.monto
        WHERE id = NEW.id;  -- Se usa la columna 'id' para relacionar con la cuenta
    ELSEIF NEW.tipo = 'gasto' THEN
        UPDATE cuentas
        SET saldo = saldo - NEW.monto
        WHERE id = NEW.id;  -- Se usa la columna 'id' para relacionar con la cuenta
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `metas`
--
ALTER TABLE `metas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `registro_gastos`
--
ALTER TABLE `registro_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gasto_id` (`gasto_id`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metas`
--
ALTER TABLE `metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `registro_gastos`
--
ALTER TABLE `registro_gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `registro_gastos`
--
ALTER TABLE `registro_gastos`
  ADD CONSTRAINT `registro_gastos_ibfk_1` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `daily_balance_report` ON SCHEDULE EVERY 1 DAY STARTS '2024-12-11 02:58:05' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Inserta un balance diario de cada cuenta
    INSERT INTO balances_diarios (cuenta_id, saldo, fecha)
    SELECT id, saldo, CURDATE() FROM cuentas;
END$$

CREATE DEFINER=`root`@`localhost` EVENT `monthly_expense_report` ON SCHEDULE EVERY 1 MONTH STARTS '2025-01-11 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Genera un reporte de gastos y ahorros para el mes anterior
    INSERT INTO reportes_mensuales (categoria_id, gasto_total, ahorro_total, fecha)
    SELECT categoria_id,
           SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) AS gasto_total,
           SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ahorro_total,
           LAST_DAY(CURDATE()) 
    FROM transacciones
    WHERE fecha >= DATE_FORMAT(CURDATE() - INTERVAL 1 MONTH, '%Y-%m-01')
    AND fecha < DATE_FORMAT(CURDATE(), '%Y-%m-01')
    GROUP BY categoria_id;
END$$

CREATE DEFINER=`root`@`localhost` EVENT `quarterly_meta_update` ON SCHEDULE EVERY 3 MONTH STARTS '2025-03-11 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Actualiza las metas financieras con nuevas proyecciones
    UPDATE metas
    SET
        ahorro_actual = ahorro_actual + (ahorro_objetivo * 0.05), -- Ejemplo de aumento de proyección
        fecha_limite = DATE_ADD(fecha_limite, INTERVAL 3 MONTH) -- Extiende el plazo de la meta
    WHERE estado = 'en progreso';
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
