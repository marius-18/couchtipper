-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: database-5018363161.webspace-host.com
-- Erstellungszeit: 08. Aug 2025 um 20:52
-- Server-Version: 8.0.36
-- PHP-Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `dbs14537804`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Datum`
--

CREATE TABLE `Datum` (
  `spieltag` int NOT NULL,
  `datum` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Ergebnisse`
--

CREATE TABLE `Ergebnisse` (
  `spieltag` int NOT NULL,
  `sp_nr` int NOT NULL,
  `tore1` int NOT NULL,
  `tore2` int NOT NULL,
  `debug_user` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `debug_ip` varchar(15) NOT NULL,
  `debug_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Precompute_Tipps`
--

CREATE TABLE `Precompute_Tipps` (
  `id` int NOT NULL,
  `spieltag` int NOT NULL,
  `value` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Precompute_Tore`
--

CREATE TABLE `Precompute_Tore` (
  `id` int NOT NULL,
  `spieltag` int NOT NULL,
  `value` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Rangliste`
--

CREATE TABLE `Rangliste` (
  `user_nr` int NOT NULL,
  `richtig` int NOT NULL,
  `tendenz` int NOT NULL,
  `differenz` int NOT NULL,
  `punkte` int NOT NULL,
  `spieltag` int NOT NULL,
  `platz` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Spieltage`
--

CREATE TABLE `Spieltage` (
  `spieltag` int NOT NULL,
  `sp_nr` int NOT NULL,
  `team1` int NOT NULL,
  `team2` int NOT NULL,
  `datum1` int DEFAULT NULL,
  `datum2` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Tabelle`
--

CREATE TABLE `Tabelle` (
  `team_nr` int NOT NULL,
  `sieg` int NOT NULL,
  `unentschieden` int NOT NULL,
  `niederlage` int NOT NULL,
  `punkte` int NOT NULL,
  `tore` int NOT NULL,
  `gegentore` int NOT NULL,
  `heim` tinyint(1) NOT NULL,
  `spieltag` int NOT NULL,
  `platz` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `Tagessieger`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `Tagessieger` (
`user_nr` int
,`punkte` int
,`spieltag` int
,`anz` bigint
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Teams`
--

CREATE TABLE `Teams` (
  `team_nr` int NOT NULL,
  `team_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `open_db_name` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `city` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `stadium` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `Teams`
--

INSERT INTO `Teams` (`team_nr`, `team_name`, `open_db_name`, `city`, `stadium`) VALUES
(1, 'FC Bayern', 'FC Bayern München', 'M&uuml;nchen', 'Allianz Arena'),
(2, 'Wolfsburg', 'VfL Wolfsburg', 'Wolfsburg', 'Volkswagen Arena'),
(3, 'Hoffenheim', 'TSG Hoffenheim', 'Sinsheim', 'PreZero Arena'),
(4, 'Augsburg', 'FC Augsburg', 'Augsburg', 'WWK Arena'),
(5, 'RaBa Leipzig', 'RB Leipzig', 'Leipzig', 'RaBa Arena'),
(6, 'M\'Gladbach', 'Borussia Mönchengladbach', 'M&ouml;nchengladbach', 'Stadion im Borussia-Park'),
(7, 'Freiburg', 'SC Freiburg', 'Freiburg', 'Europa-Park Stadion'),
(8, 'Mainz', '1. FSV Mainz 05', 'Mainz', 'Mewa Arena'),
(9, 'Schalke', 'FC Schalke 04', 'Gelsenkirchen', 'Veltins-Arena'),
(10, 'Hertha', 'Hertha BSC', 'Berlin', 'Olympiastadion'),
(11, 'Bremen', 'SV Werder Bremen', 'Bremen', 'Weserstadion'),
(12, 'Eintracht', 'Eintracht Frankfurt', 'Frankfurt', 'Deutsche Bank Park'),
(13, 'FC K&ouml;ln', '1. FC Köln', 'K&ouml;ln', 'RheinEnergieSTADION'),
(14, 'Bielefeld', 'Arminia Bielefeld', 'Bielefeld', ''),
(15, 'Union', '1. FC Union Berlin', 'Berlin', 'Stadion An der Alten F&ouml;rsterei'),
(16, 'Dortmund', 'Borussia Dortmund', 'Dortmund', 'SIGNAL IDUNA PARK'),
(17, 'Leverkusen', 'Bayer 04 Leverkusen', 'Leverkusen', 'BayArena'),
(18, 'Stuttgart', 'VfB Stuttgart', 'Stuttgart', 'Mercedes-Benz Arena'),
(19, 'F&uuml;rth', 'SpVgg Greuther Fürth', 'F&uml;rth', ''),
(20, 'Bochum', 'VfL Bochum', 'Bochum', 'Vonovia Ruhrstadion'),
(21, 'Darmstadt', 'SV Darmstadt 98', 'Darmstadt', 'Stadion am Böllenfalltor'),
(22, 'Heidenheim', '1. FC Heidenheim 1846', 'Heidenheim', 'Voith-Arena'),
(23, 'D&uuml;sseldorf', 'Fortuna Düsseldorf', 'Düsseldorf', 'Merkur SpielArena'),
(24, 'Paderborn', 'SC Paderborn 07', 'Paderborn', 'Benteler-Arena'),
(25, 'Hannover', 'Hannover 96', 'Hannover', 'Heinz von Heiden Arena'),
(26, 'N&uuml;rnberg', '1. FC Nürnberg', 'Nürnberg', 'Max-Morlock-Stadion'),
(27, 'Hamburg', 'Hamburger SV', 'Hamburg', 'Volksparkstadion'),
(28, 'Ingolstadt', 'FC Ingolstadt 04', 'Ingolstadt', 'audi sportpark'),
(29, 'Kiel', 'Holstein Kiel', 'Kiel', 'Holstein-Stadion'),
(30, 'St. Pauli', 'FC St. Pauli', 'Hamburg', 'Millerntor-Stadion');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Tipps`
--

CREATE TABLE `Tipps` (
  `spieltag` int NOT NULL,
  `sp_nr` int NOT NULL,
  `user_nr` int NOT NULL,
  `tore1` int NOT NULL,
  `tore2` int NOT NULL,
  `debug_user` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `debug_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `debug_ip` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur des Views `Tagessieger`
--
DROP TABLE IF EXISTS `Tagessieger`;

CREATE VIEW `Tagessieger`  AS SELECT `Rangliste`.`user_nr` AS `user_nr`, `Rangliste`.`punkte` AS `punkte`, `Rangliste`.`spieltag` AS `spieltag`, (select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 1))) and (`Rangliste`.`spieltag` = 1))) AS `anz` FROM `Rangliste` WHERE ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 1))) AND (`Rangliste`.`spieltag` = 1)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 2))) and (`Rangliste`.`spieltag` = 2))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 2))) and (`Rangliste`.`spieltag` = 2)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 3))) and (`Rangliste`.`spieltag` = 3))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 3))) and (`Rangliste`.`spieltag` = 3)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 4))) and (`Rangliste`.`spieltag` = 4))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 4))) and (`Rangliste`.`spieltag` = 4)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 5))) and (`Rangliste`.`spieltag` = 5))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 5))) and (`Rangliste`.`spieltag` = 5)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 6))) and (`Rangliste`.`spieltag` = 6))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 6))) and (`Rangliste`.`spieltag` = 6)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 7))) and (`Rangliste`.`spieltag` = 7))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 7))) and (`Rangliste`.`spieltag` = 7)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 8))) and (`Rangliste`.`spieltag` = 8))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 8))) and (`Rangliste`.`spieltag` = 8)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 9))) and (`Rangliste`.`spieltag` = 9))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 9))) and (`Rangliste`.`spieltag` = 9)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 10))) and (`Rangliste`.`spieltag` = 10))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 10))) and (`Rangliste`.`spieltag` = 10)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 11))) and (`Rangliste`.`spieltag` = 11))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 11))) and (`Rangliste`.`spieltag` = 11)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 12))) and (`Rangliste`.`spieltag` = 12))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 12))) and (`Rangliste`.`spieltag` = 12)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 13))) and (`Rangliste`.`spieltag` = 13))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 13))) and (`Rangliste`.`spieltag` = 13)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 14))) and (`Rangliste`.`spieltag` = 14))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 14))) and (`Rangliste`.`spieltag` = 14)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 15))) and (`Rangliste`.`spieltag` = 15))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 15))) and (`Rangliste`.`spieltag` = 15)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 16))) and (`Rangliste`.`spieltag` = 16))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 16))) and (`Rangliste`.`spieltag` = 16)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 17))) and (`Rangliste`.`spieltag` = 17))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 17))) and (`Rangliste`.`spieltag` = 17)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 18))) and (`Rangliste`.`spieltag` = 18))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 18))) and (`Rangliste`.`spieltag` = 18)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 19))) and (`Rangliste`.`spieltag` = 19))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 19))) and (`Rangliste`.`spieltag` = 19)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 20))) and (`Rangliste`.`spieltag` = 20))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 20))) and (`Rangliste`.`spieltag` = 20)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 21))) and (`Rangliste`.`spieltag` = 21))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 21))) and (`Rangliste`.`spieltag` = 21)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 22))) and (`Rangliste`.`spieltag` = 22))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 22))) and (`Rangliste`.`spieltag` = 22)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 23))) and (`Rangliste`.`spieltag` = 23))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 23))) and (`Rangliste`.`spieltag` = 23)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 24))) and (`Rangliste`.`spieltag` = 24))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 24))) and (`Rangliste`.`spieltag` = 24)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 25))) and (`Rangliste`.`spieltag` = 25))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 25))) and (`Rangliste`.`spieltag` = 25)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 26))) and (`Rangliste`.`spieltag` = 26))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 26))) and (`Rangliste`.`spieltag` = 26)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 27))) and (`Rangliste`.`spieltag` = 27))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 27))) and (`Rangliste`.`spieltag` = 27)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 28))) and (`Rangliste`.`spieltag` = 28))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 28))) and (`Rangliste`.`spieltag` = 28)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 29))) and (`Rangliste`.`spieltag` = 29))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 29))) and (`Rangliste`.`spieltag` = 29)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 30))) and (`Rangliste`.`spieltag` = 30))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 30))) and (`Rangliste`.`spieltag` = 30)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 31))) and (`Rangliste`.`spieltag` = 31))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 31))) and (`Rangliste`.`spieltag` = 31)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 32))) and (`Rangliste`.`spieltag` = 32))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 32))) and (`Rangliste`.`spieltag` = 32)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 33))) and (`Rangliste`.`spieltag` = 33))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 33))) and (`Rangliste`.`spieltag` = 33)) union all select `Rangliste`.`user_nr` AS `user_nr`,`Rangliste`.`punkte` AS `punkte`,`Rangliste`.`spieltag` AS `spieltag`,(select count(`Rangliste`.`spieltag`) from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 34))) and (`Rangliste`.`spieltag` = 34))) AS `anz` from `Rangliste` where ((`Rangliste`.`punkte` = (select max(`Rangliste`.`punkte`) from `Rangliste` where (`Rangliste`.`spieltag` = 34))) and (`Rangliste`.`spieltag` = 34)) ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Datum`
--
ALTER TABLE `Datum`
  ADD PRIMARY KEY (`spieltag`);

--
-- Indizes für die Tabelle `Ergebnisse`
--
ALTER TABLE `Ergebnisse`
  ADD PRIMARY KEY (`spieltag`,`sp_nr`);

--
-- Indizes für die Tabelle `Precompute_Tipps`
--
ALTER TABLE `Precompute_Tipps`
  ADD PRIMARY KEY (`id`,`spieltag`);

--
-- Indizes für die Tabelle `Precompute_Tore`
--
ALTER TABLE `Precompute_Tore`
  ADD PRIMARY KEY (`id`,`spieltag`);

--
-- Indizes für die Tabelle `Rangliste`
--
ALTER TABLE `Rangliste`
  ADD PRIMARY KEY (`user_nr`,`spieltag`);

--
-- Indizes für die Tabelle `Spieltage`
--
ALTER TABLE `Spieltage`
  ADD PRIMARY KEY (`spieltag`,`sp_nr`);

--
-- Indizes für die Tabelle `Tabelle`
--
ALTER TABLE `Tabelle`
  ADD PRIMARY KEY (`team_nr`,`spieltag`);

--
-- Indizes für die Tabelle `Teams`
--
ALTER TABLE `Teams`
  ADD PRIMARY KEY (`team_nr`);

--
-- Indizes für die Tabelle `Tipps`
--
ALTER TABLE `Tipps`
  ADD PRIMARY KEY (`spieltag`,`sp_nr`,`user_nr`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Datum`
--
ALTER TABLE `Datum`
  MODIFY `spieltag` int NOT NULL AUTO_INCREMENT;
COMMIT;

--
-- AUTO_INCREMENT für Tabelle `Teams`
--
ALTER TABLE `Teams`
  MODIFY `team_nr` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
