-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: MySQL
-- Tempo de geração: 04/03/2024 às 20:28
-- Versão do servidor: 5.6.51
-- Versão do PHP: 8.2.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `habits_db`
--

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_create` (
  `p_name` VARCHAR(64), 
  `p_email` VARCHAR(128),
  `p_password` VARCHAR(256)
)   
BEGIN  
  INSERT INTO users (`name`, `email`, `password`) 
  VALUES (p_name, p_email, p_password);
  
  SELECT * FROM users WHERE id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_habits_create`(
  IN `p_habit_title` VARCHAR(64), 
  IN `p_week_days_string` VARCHAR(20), 
  IN `p_user_id` INT
)
BEGIN
  DECLARE v_habit_id INT;
  DECLARE v_week_day INT;
  DECLARE v_comma_position INT;

  -- Criar o hábito com a data atual, começando do início do dia
  INSERT INTO habits (`title`, `created_at`, `user_id`) VALUES (p_habit_title, CURRENT_DATE(), p_user_id);

  -- Obter o ID do hábito recém-criado
  SET v_habit_id = LAST_INSERT_ID();

  -- Associar o hábito a cada dia
  WHILE LENGTH(p_week_days_string) > 0 DO
    SET v_comma_position = LOCATE(',', p_week_days_string);

    IF v_comma_position = 0 THEN
      SET v_week_day = p_week_days_string;
    ELSE
      SET v_week_day = SUBSTRING(p_week_days_string, 1, v_comma_position - 1);
    END IF;

    -- Inserir a associação do hábito ao dia
    INSERT INTO habit_week_days (`habit_id`, `week_day`) VALUES (v_habit_id, v_week_day);

    -- Atualizar a string de dias para o próximo
    IF v_comma_position = 0 THEN
      SET p_week_days_string = '';
    ELSE
      SET p_week_days_string = SUBSTRING(p_week_days_string, v_comma_position + 1);
    END IF;
  END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_habits_toggle`(
  IN `p_user_id` INT, 
  IN `p_habit_id` INT,
  IN `p_date` DATETIME
)
BEGIN
  DECLARE v_day_id INT;
  DECLARE v_day_habit_id INT;

  -- Verifica se existe um registro para o dia atual
  INSERT IGNORE INTO days (`date`) VALUES (p_date);
  SELECT `id` INTO v_day_id FROM days WHERE `date` = p_date;

  -- Verifica se o hábito já foi marcado para o dia atual
  SELECT `id` INTO v_day_habit_id
  FROM day_habits
  WHERE `day_id` = v_day_id AND `habit_id` = p_habit_id;

  -- Toggle do hábito
  IF v_day_habit_id IS NOT NULL THEN
    -- Se o hábito já estiver marcado, desmarca
    DELETE FROM day_habits WHERE `id` = v_day_habit_id;
  ELSE
    -- Se o hábito não estiver marcado, marca
    INSERT INTO day_habits (`day_id`, `habit_id`) VALUES (v_day_id, p_habit_id);
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_passwords_recoveries_create` (
  `p_user_id` INT, 
  `p_ip` VARCHAR(45)
)   
BEGIN  
  INSERT INTO users_passwords_recoveries (user_id, ip)
  VALUES(p_user_id, p_ip);
  
  SELECT * FROM users_passwords_recoveries
  WHERE id = LAST_INSERT_ID();
END$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `days`
--

CREATE TABLE `days` (
  `id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `day_habits`
--

CREATE TABLE `day_habits` (
  `id` int(11) NOT NULL,
  `day_id` int(11) DEFAULT NULL,
  `habit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `habits`
--

CREATE TABLE `habits` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `habit_week_days`
--

CREATE TABLE `habit_week_days` (
  `id` int(11) NOT NULL,
  `habit_id` int(11) DEFAULT NULL,
  `week_day` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users_logs`
--

CREATE TABLE `users_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log` varchar(128) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` varchar(128) NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `url` varchar(128) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users_passwords_recoveries`
--

CREATE TABLE `users_passwords_recoveries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `recovery_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  ADD KEY `fk_day_habits_days` (`day_id`),
  ADD KEY `fk_day_habits_habits` (`habit_id`);

--
-- Índices de tabela `habits`
--
ALTER TABLE `habits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `habit_week_days`
--
ALTER TABLE `habit_week_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `habit_id` (`habit_id`,`week_day`),
  ADD KEY `fk_habit_week_days_habits` (`habit_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `users_logs`
--
ALTER TABLE `users_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_logs_users_idx` (`user_id`);

--
-- Índices de tabela `users_passwords_recoveries`
--
ALTER TABLE `users_passwords_recoveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_passwords_recoveries_users_idx` (`user_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `days`
--
ALTER TABLE `days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `day_habits`
--
ALTER TABLE `day_habits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `habits`
--
ALTER TABLE `habits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `habit_week_days`
--
ALTER TABLE `habit_week_days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users_logs`
--
ALTER TABLE `users_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users_passwords_recoveries`
--
ALTER TABLE `users_passwords_recoveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `day_habits`
--
ALTER TABLE `day_habits`
  ADD CONSTRAINT `fk_day_habits_days` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`),
  ADD CONSTRAINT `fk_day_habits_habits` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`);

--
-- Restrições para tabelas `habits`
--
ALTER TABLE `habits`
  ADD CONSTRAINT `fk_habits_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `habit_week_days`
--
ALTER TABLE `habit_week_days`
  ADD CONSTRAINT `fk_habit_week_days_habits` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`);
COMMIT;

--
-- Restrições para tabelas `users_logs`
--
ALTER TABLE `users_logs`
  ADD CONSTRAINT `fk_users_logs_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `users_passwords_recoveries`
--
ALTER TABLE `users_passwords_recoveries`
  ADD CONSTRAINT `fk_users_passwords_recoveries_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
