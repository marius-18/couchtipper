-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: database-5020488807.webspace-host.com
-- Erstellungszeit: 19. Mai 2026 um 07:38
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
-- Datenbank: `dbs15689695`
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
  `debug_user` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `debug_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `debug_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Precompute_Tipps`
--

CREATE TABLE `Precompute_Tipps` (
  `id` int NOT NULL,
  `spieltag` int NOT NULL,
  `value` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Precompute_Tore`
--

CREATE TABLE `Precompute_Tore` (
  `id` int NOT NULL,
  `spieltag` int NOT NULL,
  `value` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
-- Tabellenstruktur für Tabelle `Spielorte`
--

CREATE TABLE `Spielorte` (
  `id` int NOT NULL,
  `stadt` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `stadion` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
  `spielort` int NOT NULL
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
-- Tabellenstruktur für Tabelle `Teams`
--

CREATE TABLE `Teams` (
  `team_nr` int NOT NULL,
  `team_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `gruppe` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `position` int NOT NULL,
  `open_db_name` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `Teams`
--

INSERT INTO `Teams` (`team_nr`, `team_name`, `gruppe`, `position`, `open_db_name`) VALUES
(1, 'Russland', '', 0, 'Russland'),
(2, 'Saudi-Arabien', '', 0, 'Saudi-Arabien'),
(3, '&Auml;gypten', '', 0, 'Ägypten'),
(4, 'Uruguay', '', 0, 'Uruguay'),
(5, 'Marokko', '', 0, 'Marokko'),
(6, 'Iran', '', 0, 'Iran'),
(7, 'Portugal', '', 0, 'Portugal'),
(8, 'Spanien', '', 0, 'Spanien'),
(9, 'Frankreich', '', 0, 'Frankreich'),
(10, 'Australien', '', 0, 'Australien'),
(11, 'Peru', '', 0, 'Peru'),
(12, 'D&auml;nemark', '', 0, 'Dänemark'),
(13, 'Argentinien', '', 0, 'Argentinien'),
(14, 'Island', '', 0, 'Island'),
(15, 'Kroatien', '', 0, 'Kroatien'),
(16, 'Nigeria', '', 0, 'Nigeria'),
(17, 'Costa Rica', '', 0, 'Costa Rica'),
(18, 'Serbien', '', 0, 'Serbien'),
(19, 'Brasilien', '', 0, 'Brasilien'),
(20, 'Schweiz', '', 0, 'Schweiz'),
(21, 'Deutschland', '', 0, 'Deutschland'),
(22, 'Mexiko', '', 0, 'Mexiko'),
(23, 'Schweden', '', 0, 'Schweden'),
(24, 'S&uuml;dkorea', '', 0, 'Südkorea'),
(25, 'Belgien', '', 0, 'Belgien'),
(26, 'Panama', '', 0, 'Panama'),
(27, 'Tunesien', '', 0, 'Tunesien'),
(28, 'England', '', 0, 'England'),
(29, 'Polen', '', 0, 'Polen'),
(30, 'Senegal', '', 0, 'Senegal'),
(31, 'Kolumbien', '', 0, 'Kolumbien'),
(32, 'Japan', '', 0, 'Japan'),

(-1,  'Sieger A', '', 0, ''),
(-2,  'Zweiter A', '', 0, ''),
(-3,  'Sieger B', '', 0, ''),
(-4,  'Zweiter B', '', 0, ''),
(-5,  'Sieger C', '', 0, ''),
(-6,  'Zweiter C', '', 0, ''),
(-7,  'Sieger D', '', 0, ''),
(-8,  'Zweiter D', '', 0, ''),
(-9,  'Sieger E', '', 0, ''),
(-10, 'Zweiter E', '', 0, ''),
(-11, 'Sieger F', '', 0, ''),
(-12, 'Zweiter F', '', 0, ''),
(-13, 'Sieger G', '', 0, ''),
(-14, 'Zweiter G', '', 0, ''),
(-15, 'Sieger H', '', 0, ''),
(-16, 'Zweiter H', '', 0, ''),
(-17, 'Sieger I', '', 0, ''),
(-18, 'Zweiter I', '', 0, ''),
(-19, 'Sieger J', '', 0, ''),
(-20, 'Zweiter J', '', 0, ''),
(-21, 'Sieger K', '', 0, ''),
(-22, 'Zweiter K', '', 0, ''),
(-23, 'Sieger L', '', 0, ''),
(-24, 'Zweiter L', '', 0, ''),

-- beste Gruppendritte
(-25, 'Bester Dritter 1', '', 0, ''),
(-26, 'Bester Dritter 2', '', 0, ''),
(-27, 'Bester Dritter 3', '', 0, ''),
(-28, 'Bester Dritter 4', '', 0, ''),
(-29, 'Bester Dritter 5', '', 0, ''),
(-30, 'Bester Dritter 6', '', 0, ''),
(-31, 'Bester Dritter 7', '', 0, ''),
(-32, 'Bester Dritter 8', '', 0, ''),

-- Sechzehntelfinale (32 Teams → 16 Sieger)
(-33, 'Sieger 16F 1', '', 0, ''),
(-34, 'Sieger 16F 2', '', 0, ''),
(-35, 'Sieger 16F 3', '', 0, ''),
(-36, 'Sieger 16F 4', '', 0, ''),
(-37, 'Sieger 16F 5', '', 0, ''),
(-38, 'Sieger 16F 6', '', 0, ''),
(-39, 'Sieger 16F 7', '', 0, ''),
(-40, 'Sieger 16F 8', '', 0, ''),
(-41, 'Sieger 16F 9', '', 0, ''),
(-42, 'Sieger 16F 10', '', 0, ''),
(-43, 'Sieger 16F 11', '', 0, ''),
(-44, 'Sieger 16F 12', '', 0, ''),
(-45, 'Sieger 16F 13', '', 0, ''),
(-46, 'Sieger 16F 14', '', 0, ''),
(-47, 'Sieger 16F 15', '', 0, ''),
(-48, 'Sieger 16F 16', '', 0, ''),

-- Achtelfinale
(-49, 'Sieger AF 1', '', 0, ''),
(-50, 'Sieger AF 2', '', 0, ''),
(-51, 'Sieger AF 3', '', 0, ''),
(-52, 'Sieger AF 4', '', 0, ''),
(-53, 'Sieger AF 5', '', 0, ''),
(-54, 'Sieger AF 6', '', 0, ''),
(-55, 'Sieger AF 7', '', 0, ''),
(-56, 'Sieger AF 8', '', 0, ''),

-- Viertelfinale
(-57, 'Sieger VF 1', '', 0, ''),
(-58, 'Sieger VF 2', '', 0, ''),
(-59, 'Sieger VF 3', '', 0, ''),
(-60, 'Sieger VF 4', '', 0, ''),

-- Halbfinale
(-61, 'Sieger HF 1', '', 0, ''),
(-62, 'Verlierer HF 1', '', 0, ''),
(-63, 'Sieger HF 2', '', 0, ''),
(-64, 'Verlierer HF 2', '', 0, '');
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
  `debug_user` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `debug_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `debug_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Indizes für die Tabelle `Spielorte`
--
ALTER TABLE `Spielorte`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT für Tabelle `Spielorte`
--
ALTER TABLE `Spielorte`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

--
-- AUTO_INCREMENT für Tabelle `Teams`
--
ALTER TABLE `Teams`
MODIFY `team_nr` INT NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
