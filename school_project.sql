-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 05:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `coach`
--

CREATE TABLE `coach` (
  `TeamName` varchar(50) NOT NULL,
  `CoachName` varchar(100) NOT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `Salary` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `GameID` int(11) NOT NULL,
  `HomeTeam` varchar(50) NOT NULL,
  `AwayTeam` varchar(50) NOT NULL,
  `Winner` varchar(50) DEFAULT NULL,
  `GameType` varchar(30) DEFAULT NULL,
  `GameDate` date DEFAULT NULL,
  `StadiumID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gamestats`
--

CREATE TABLE `gamestats` (
  `GameID` int(11) NOT NULL,
  `TeamName` varchar(50) NOT NULL,
  `TurnoversForced` int(11) DEFAULT NULL,
  `PenaltyYards` int(11) DEFAULT NULL,
  `RushingYards` int(11) DEFAULT NULL,
  `PassingYards` int(11) DEFAULT NULL,
  `TotalPoints` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `TeamName` varchar(50) NOT NULL,
  `PlayerNumber` int(11) NOT NULL,
  `PlayerName` varchar(100) DEFAULT NULL,
  `Position` varchar(30) DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `Salary` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referee`
--

CREATE TABLE `referee` (
  `RefID` int(11) NOT NULL,
  `RefName` varchar(100) DEFAULT NULL,
  `Role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stadium`
--

CREATE TABLE `stadium` (
  `StadiumID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `City` varchar(50) NOT NULL,
  `Capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `TeamName` varchar(50) NOT NULL,
  `Division` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`TeamName`, `Division`) VALUES
('Kansas City Chiefs', 'AFC West');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Username` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `FavoriteTeam` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Username`, `Password`, `FavoriteTeam`) VALUES
('admin', 'password123', 'Kansas City Chiefs');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coach`
--
ALTER TABLE `coach`
  ADD PRIMARY KEY (`TeamName`,`CoachName`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`GameID`),
  ADD KEY `HomeTeam` (`HomeTeam`),
  ADD KEY `AwayTeam` (`AwayTeam`),
  ADD KEY `Winner` (`Winner`),
  ADD KEY `StadiumID` (`StadiumID`);

--
-- Indexes for table `gamestats`
--
ALTER TABLE `gamestats`
  ADD PRIMARY KEY (`GameID`,`TeamName`),
  ADD KEY `TeamName` (`TeamName`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`TeamName`,`PlayerNumber`);

--
-- Indexes for table `referee`
--
ALTER TABLE `referee`
  ADD PRIMARY KEY (`RefID`);

--
-- Indexes for table `stadium`
--
ALTER TABLE `stadium`
  ADD PRIMARY KEY (`StadiumID`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`TeamName`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`),
  ADD KEY `FavoriteTeam` (`FavoriteTeam`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `GameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stadium`
--
ALTER TABLE `stadium`
  MODIFY `StadiumID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `coach`
--
ALTER TABLE `coach`
  ADD CONSTRAINT `coach_ibfk_1` FOREIGN KEY (`TeamName`) REFERENCES `teams` (`TeamName`);

--
-- Constraints for table `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`HomeTeam`) REFERENCES `teams` (`TeamName`),
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`AwayTeam`) REFERENCES `teams` (`TeamName`),
  ADD CONSTRAINT `game_ibfk_3` FOREIGN KEY (`Winner`) REFERENCES `teams` (`TeamName`),
  ADD CONSTRAINT `game_ibfk_4` FOREIGN KEY (`StadiumID`) REFERENCES `stadium` (`StadiumID`);

--
-- Constraints for table `gamestats`
--
ALTER TABLE `gamestats`
  ADD CONSTRAINT `gamestats_ibfk_1` FOREIGN KEY (`GameID`) REFERENCES `game` (`GameID`),
  ADD CONSTRAINT `gamestats_ibfk_2` FOREIGN KEY (`TeamName`) REFERENCES `teams` (`TeamName`);

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`TeamName`) REFERENCES `teams` (`TeamName`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`FavoriteTeam`) REFERENCES `teams` (`TeamName`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
