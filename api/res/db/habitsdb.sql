-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 19/11/2023 às 14:58
-- Versão do servidor: 8.2.0
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `habitsdb`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`%` PROCEDURE `CreateHabitAndAssociateWeekDays` (IN `habitTitle` VARCHAR(255), IN `weekDaysString` VARCHAR(20))   BEGIN
  DECLARE habitId INT;
  DECLARE weekDay INT;
  DECLARE commaPosition INT;

  -- Criar o hábito com a data atual, começando do início do dia
  INSERT INTO habits (title, created_at) VALUES (habitTitle, CURRENT_DATE());

  -- Obter o ID do hábito recém-criado
  SET habitId = LAST_INSERT_ID();

  -- Associar o hábito a cada dia
  WHILE LENGTH(weekDaysString) > 0 DO
    SET commaPosition = LOCATE(',', weekDaysString);
    IF commaPosition = 0 THEN
      SET weekDay = weekDaysString;
    ELSE
      SET weekDay = SUBSTRING(weekDaysString, 1, commaPosition - 1);
    END IF;

    -- Inserir a associação do hábito ao dia
    INSERT INTO habit_week_days (habit_id, week_day) VALUES (habitId, weekDay);

    -- Atualizar a string de dias para o próximo
    IF commaPosition = 0 THEN
      SET weekDaysString = '';
    ELSE
      SET weekDaysString = SUBSTRING(weekDaysString, commaPosition + 1);
    END IF;
  END WHILE;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `ToggleHabitForDay` (IN `habitId` INT)   BEGIN
  DECLARE today DATETIME;
  DECLARE dayId INT;
  DECLARE dayHabitId INT;

  SET today = CURDATE();

  -- Verifica se existe um registro para o dia atual
  INSERT IGNORE INTO days (date) VALUES (today);
  SELECT id INTO dayId FROM days WHERE date = today;

  -- Verifica se o hábito já foi marcado para o dia atual
  SELECT id INTO dayHabitId
  FROM day_habits
  WHERE day_id = dayId AND habit_id = habitId;

  -- Toggle do hábito
  IF dayHabitId IS NOT NULL THEN
    -- Se o hábito já estiver marcado, desmarca
    DELETE FROM day_habits WHERE id = dayHabitId;
  ELSE
    -- Se o hábito não estiver marcado, marca
    INSERT INTO day_habits (day_id, habit_id) VALUES (dayId, habitId);
  END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `days`
--

CREATE TABLE `days` (
  `id` int NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `days`
--

INSERT INTO `days` (`id`, `date`) VALUES
(1, '2023-11-19 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `day_habits`
--

CREATE TABLE `day_habits` (
  `id` int NOT NULL,
  `day_id` int DEFAULT NULL,
  `habit_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `day_habits`
--

INSERT INTO `day_habits` (`id`, `day_id`, `habit_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `habits`
--

CREATE TABLE `habits` (
  `id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `habits`
--

INSERT INTO `habits` (`id`, `title`, `created_at`) VALUES
(1, 'Beber 2L de água', '2023-11-19 00:00:00'),
(2, 'Ler por 1H', '2023-11-19 00:00:00'),
(3, 'Estudar música', '2023-11-19 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `habit_week_days`
--

CREATE TABLE `habit_week_days` (
  `id` int NOT NULL,
  `habit_id` int DEFAULT NULL,
  `week_day` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `habit_week_days`
--

INSERT INTO `habit_week_days` (`id`, `habit_id`, `week_day`) VALUES
(1, 1, 2),
(2, 1, 4),
(3, 1, 6),
(4, 2, 1),
(5, 2, 3),
(6, 2, 5),
(7, 3, 2),
(8, 3, 3),
(9, 3, 4),
(10, 3, 5);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `days`
--
ALTER TABLE `days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`);

--
-- Índices de tabela `day_habits`
--
ALTER TABLE `day_habits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `day_id` (`day_id`,`habit_id`),
  ADD KEY `habit_id` (`habit_id`);

--
-- Índices de tabela `habits`
--
ALTER TABLE `habits`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `habit_week_days`
--
ALTER TABLE `habit_week_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `habit_id` (`habit_id`,`week_day`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `days`
--
ALTER TABLE `days`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `day_habits`
--
ALTER TABLE `day_habits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `habits`
--
ALTER TABLE `habits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `habit_week_days`
--
ALTER TABLE `habit_week_days`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `day_habits`
--
ALTER TABLE `day_habits`
  ADD CONSTRAINT `day_habits_ibfk_1` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`),
  ADD CONSTRAINT `day_habits_ibfk_2` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`);

--
-- Restrições para tabelas `habit_week_days`
--
ALTER TABLE `habit_week_days`
  ADD CONSTRAINT `habit_week_days_ibfk_1` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
