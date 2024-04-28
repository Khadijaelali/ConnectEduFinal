-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2024 at 10:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `annonces`
--

INSERT INTO `annonces` (`id`, `course_id`, `message`, `date_creation`) VALUES
(1, 42, 'hi', '2024-03-26 23:02:34'),
(2, 42, 'welcome to my class everyone !!! ', '2024-03-26 23:17:29'),
(8, 42, 'hello word', '2024-03-26 23:53:26'),
(9, 42, 'bonjour\r\n', '2024-03-27 00:04:51'),
(10, 43, 'salut\r\n', '2024-03-27 00:47:23'),
(11, 44, 'salut', '2024-03-27 23:53:29'),
(12, 45, 'hello world', '2024-03-28 00:31:14'),
(13, 44, 'hzllo word', '2024-03-30 01:39:23'),
(14, 46, 'pas de cour cette semain', '2024-04-22 15:22:25'),
(15, 46, 'salut touts le monde', '2024-04-23 02:02:08');

-- --------------------------------------------------------

--
-- Table structure for table `annonce_comments`
--

CREATE TABLE `annonce_comments` (
  `id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `utilisateur_id` varchar(255) NOT NULL,
  `commentaire` text NOT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `annonce_comments`
--

INSERT INTO `annonce_comments` (`id`, `annonce_id`, `utilisateur_id`, `commentaire`, `date_creation`) VALUES
(1, 9, 'test', 'salut', '2024-04-12 19:34:43'),
(2, 9, 'test', 'merci', '2024-04-12 19:35:01'),
(3, 9, 'test', 'bonjour', '2024-04-12 19:37:14'),
(4, 9, 'test', 'bonjour', '2024-04-12 19:37:20'),
(5, 9, 'test', 'bonjour', '2024-04-12 19:37:21');

-- --------------------------------------------------------

--
-- Table structure for table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `type` enum('cours','annonce') NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `commentaire` text NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `commentaires`
--

INSERT INTO `commentaires` (`id`, `parent_id`, `type`, `user_id`, `commentaire`, `date_creation`) VALUES
(19, 17, '', 'elouardani', 'salut tout le monde', '2024-03-27 23:57:08'),
(23, 18, '', 'elouardani', 'oui', '2024-03-28 00:51:47'),
(28, 8, '', 'test', 'bonjour les amis', '2024-03-28 04:38:42'),
(30, 8, '', 'test', 'hi', '2024-03-28 23:34:57'),
(45, 11, '', 'test', 'hello', '2024-03-29 00:47:56'),
(46, 11, '', 'test', 'how are u doing', '2024-03-29 00:48:07'),
(47, 8, '', 'test', 'hello', '2024-03-29 00:52:39'),
(48, 17, '', 'elouardani', 'jéspére que vous allez bien', '2024-03-29 00:56:54'),
(50, 17, '', 'elouardani', 'hi', '2024-03-29 01:08:16'),
(51, 9, '', 'test', 'heelo world', '2024-03-29 23:40:07'),
(52, 8, '', 'test', 'hello', '2024-03-29 23:48:48'),
(53, 8, '', 'test', 'hello', '2024-03-29 23:49:57'),
(54, 14, '', 'test', 'bonjour', '2024-03-30 01:30:33'),
(55, 14, '', 'test', 'bonjour', '2024-03-30 01:30:35'),
(56, 13, '', 'test', 'hello', '2024-03-30 01:30:45'),
(57, 13, '', 'test', 'how yall doing', '2024-03-30 01:31:06'),
(58, 13, '', 'test', 'thank you', '2024-03-30 01:42:45'),
(59, 14, '', 'test', 'aksh', '2024-03-31 22:13:53'),
(60, 14, '', 'test', 'welcome', '2024-03-31 23:19:37'),
(61, 14, '', 'test', 'ksks', '2024-04-01 00:21:22'),
(62, 21, '', 'test', 'merci', '2024-04-05 00:59:57'),
(63, 23, '', 'test', 'merci', '2024-04-11 23:39:54'),
(64, 23, '', 'test', 'bonjour', '2024-04-12 19:34:25'),
(65, 17, '', 'elouardani', 'hi', '2024-04-13 14:35:09'),
(66, 17, '', 'elouardani', 'khadija', '2024-04-13 14:37:11'),
(67, 24, '', 'kenza_Mam', 'merci', '2024-04-16 20:16:38'),
(68, 26, '', 'fatima-El', 'merci Prof', '2024-04-19 03:19:20'),
(69, 11, '', 'elali', 'merci', '2024-04-19 03:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `section` varchar(255) DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `salle` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `professeur_id` varchar(255) DEFAULT NULL,
  `code_cours` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cours`
--

INSERT INTO `cours` (`id`, `titre`, `section`, `sujet`, `salle`, `date_creation`, `professeur_id`, `code_cours`) VALUES
(42, 'IOT', 'D', 'test', '10', '2024-03-23 22:07:27', 'test', 'HZF1POVVMH'),
(43, 'DEV', '2', 'les class', '10', '2024-03-23 23:15:27', 'test', 'OBVT7O5TOV'),
(44, 'POO', '2', 'les class', '10', '2024-03-26 23:36:26', 'elouardani', '7SUVRE4G9Z'),
(45, 'Réseaux', '2', 'les fibre optique', '10', '2024-03-28 00:24:27', 'elouardani', 'JQ9AROTBZC'),
(46, 'Les antennes', '2', 'hyperfrequence', '10', '2024-04-13 23:31:04', 'kenza_Mam', 'ZO75U7U27I'),
(47, 'Management', '2', 'cahier de charge', '10', '2024-04-16 20:23:01', 'khadija-El', 'OCK00I9MUR');

-- --------------------------------------------------------

--
-- Table structure for table `devoir`
--

CREATE TABLE `devoir` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_limite` datetime NOT NULL,
  `cours_id` int(11) NOT NULL,
  `professeur_id` varchar(255) NOT NULL,
  `fichier_chemin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `devoir`
--

INSERT INTO `devoir` (`id`, `titre`, `description`, `date_limite`, `cours_id`, `professeur_id`, `fichier_chemin`) VALUES
(14, 'devoir libre', 'ce devoir tester votre capaciter a resoudre des problem lier a xxx', '2024-05-26 00:00:00', 46, 'kenza_Mam', 'uploads_Dev/c30ffb57c13542e8cbdeed5fd32be491.pdf'),
(15, 'anglais', 'writing', '2024-06-20 00:00:00', 46, 'kenza_Mam', 'uploads/af4a5b94a748e233298d88e759d56fcd.pdf'),
(16, 'php', 'voici le projet de cete semain ', '2024-06-24 00:00:00', 46, 'kenza_Mam', 'uploads_Dev/b03e3a68d591990a6dddf6495b7d1d05.txt'),
(17, 'math', 'les fonction et les suites', '2024-04-24 09:30:00', 46, 'kenza_Mam', 'uploads_Dev/dc3006c0d56eb06343d2c739800daa9c.docx'),
(18, 'physic', 'les ondes', '2024-04-24 09:30:00', 46, 'kenza_Mam', 'uploads_Dev/963489ad08be08983c1b301a97b45319.docx');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id_materiel` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id_materiel`, `course_id`, `file_name`, `file_path`, `description`, `uploaded_at`, `file_type`) VALUES
(8, 42, 'Partie 2 Emetteurs recepteur optique.pptx (2).pdf', 'uploads/47fbf26a0581d454cfd9b239e290ce9a.pdf', 'Bonjour!!', '2024-03-23 22:08:08', NULL),
(9, 42, 'Partie 1 Constitution et caracte?ristiquues FO (1).pdf', 'uploads/fa513e966b5708af374c7153c9ba7137.pdf', 'voici le cour ', '2024-03-23 22:54:51', NULL),
(10, 42, 'Dev_04_EFM_V1 (1).pdf', 'uploads/bbd42213b0812739bc8d6b213c5d2c8b.pdf', '', '2024-03-23 23:16:17', NULL),
(11, 42, 'Partie 1 Constitution et caracte?ristiquues FO.pdf', 'uploads/71971958de60ff3128c76ff826fc5178.pdf', '', '2024-03-23 23:16:35', NULL),
(12, 42, 'cours_modulations-numériques (2).pdf', 'uploads/85d88f816006e8c171749d69d2ed52fc.pdf', '', '2024-03-23 23:16:50', NULL),
(13, 42, 'Chapitre4_segmentation_231218_102942 (1).pdf', 'uploads/0949b11601b36c03d1e7daa930633293.pdf', '', '2024-03-23 23:17:06', NULL),
(14, 42, 'Chapitre 2_Terminologie des réseaux_231218_102830.pdf', 'uploads/1a6131a3efa4a056476c95c32dfa2153.pdf', '', '2024-03-23 23:17:23', NULL),
(15, 43, 'compl-ment connexion fibre (1).pdf', 'uploads/d66b62f7148cb86528096880d791d1cf.pdf', 'voici le cour ', '2024-03-26 23:32:45', NULL),
(16, 43, 'Dev_04_EFM_V1 (1).pdf', 'uploads/c5ce99c240c6b1304398d53cdd2c7f80.pdf', 'it works', '2024-03-26 23:45:31', NULL),
(17, 44, 'compl-ment connexion fibre (1).pdf', 'uploads/5ffabdfa64c6ff428f05902cea848cce.pdf', 'bonjour ', '2024-03-27 23:32:24', NULL),
(18, 45, 'Partie 1 Constitution et caracte?ristiquues FO (1).pdf', 'uploads/2e0c670dee44191b981e9c1d163acf1a.pdf', 'voici le cour de cette semain', '2024-03-28 00:26:43', NULL),
(20, 42, 'modele2.png', 'uploads/6acc8810d4c9de0dc321b4057390912b.png', '', '2024-04-05 00:30:29', NULL),
(21, 42, 'gant.png', 'uploads/94ca8e7cf84a97b72b3c070a7dc24aa6.png', 'hello voici le diagrame ', '2024-04-05 00:57:30', 'png'),
(22, 42, 'WhatsApp Image 2024-02-27 at 22.21.37.jpeg', 'uploads/5c3cf17688a20186a9a19355895ce34e.jpeg', 'salut tout le monde', '2024-04-05 00:58:52', 'jpeg'),
(23, 42, 'WhatsApp Image 2024-03-01 at 23.59.48.jpeg', 'uploads/25980984cde3755d1ac474319b68d62c.jpeg', 'voici le cour de cette semain', '2024-04-11 22:17:08', 'jpeg'),
(24, 46, 'CamScanner 04-03-2024 19.55.pdf', 'uploads/90fe9a09b1c96c80b7440a0c85c46015.pdf', 'bonjour', '2024-04-16 20:08:55', 'pdf'),
(25, 47, 'WhatsApp Image 2024-03-22 at 17.06.56 (2).jpeg', 'uploads/57364e96b2aa970dcf5f169a5d80958f.jpeg', '', '2024-04-16 20:44:46', 'jpeg'),
(26, 46, 'PFA_2022-23_ITIRC_GAOUCHE Nousseiba MAHMOUDI Manal.pdf', 'uploads/53bdc55717c8e327d19210aa0a307ae5.pdf', '', '2024-04-16 23:46:09', 'pdf'),
(27, 46, 'Mémoire de PFE_finale (1).pdf', 'uploads/0f8230a8022777ec73cb2516cc4a5430.pdf', '', '2024-04-16 23:46:09', 'pdf'),
(28, 46, 'Examen1_ComNumAvancees_Avril2024.pdf', 'uploads/026a8f5776cc8d4cafb8ed5769d286c1.pdf', '', '2024-04-23 02:02:08', 'pdf'),
(29, 46, 'Examen1_ComNumAvancees_Avril2024.pdf', 'uploads/0107924e56a8486da6330baa5b9ef5eb.pdf', 'hello etudiant', '2024-04-23 02:25:39', 'pdf');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_list`
--

CREATE TABLE `schedule_list` (
  `id` int(30) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_list`
--

INSERT INTO `schedule_list` (`id`, `title`, `description`, `start_datetime`, `end_datetime`) VALUES
(1, 'Sample 101', 'This is a sample schedule only.', '2022-01-10 10:30:00', '2022-01-11 18:00:00'),
(2, 'Sample 102', 'Sample Description 102', '2022-01-08 09:30:00', '2022-01-08 11:30:00'),
(4, 'Sample 102', 'Sample Description', '2022-01-12 14:00:00', '2022-01-12 17:00:00'),
(1, 'Sample 101', 'This is a sample schedule only.', '2022-01-10 10:30:00', '2022-01-11 18:00:00'),
(2, 'Sample 102', 'Sample Description 102', '2022-01-08 09:30:00', '2022-01-08 11:30:00'),
(4, 'Sample 102', 'Sample Description', '2022-01-12 14:00:00', '2022-01-12 17:00:00'),
(0, 'examen', 'preparez vous', '2024-06-21 08:30:00', '2024-06-21 10:30:00'),
(0, 'anglais', 'writing', '2024-06-21 10:00:00', '2024-06-21 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `soumissions_devoir`
--

CREATE TABLE `soumissions_devoir` (
  `soumission_id` int(11) NOT NULL,
  `utilisateur_id` varchar(255) DEFAULT NULL,
  `devoir_id` int(11) NOT NULL,
  `fichier_chemin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `soumissions_devoir`
--

INSERT INTO `soumissions_devoir` (`soumission_id`, `utilisateur_id`, `devoir_id`, `fichier_chemin`) VALUES
(12, 'fatima-El', 16, './uploads_Dev/09d66730df9a098fc3591033e8350cb7.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `student_course_access`
--

CREATE TABLE `student_course_access` (
  `student_id` varchar(255) NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `access_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_course_access`
--

INSERT INTO `student_course_access` (`student_id`, `course_code`, `access_time`) VALUES
('fatima-El', 'HZF1POVVMH', '2024-04-28 03:48:04'),
('fatima-El', 'ZO75U7U27I', '2024-04-28 03:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `utilisateur_id` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Etudiant','Professeur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`utilisateur_id`, `nom`, `prenom`, `password`, `role`) VALUES
('', 'zenati', 'kenza', '$2y$10$DkC0EQMBB02pG0JHCaEGFeamJltJEEtbrg6IKqneLev7HdA5Sk9ba', 'Etudiant'),
('elali', 'khadija', 'elali', 'password', 'Etudiant'),
('elouardani', 'mohammed', 'elouardani', 'password', 'Professeur'),
('fatima-El', 'fatima', 'elali', '$2y$10$lYM/rWlr7iYISJS/8LnUZOSAKJipUtGd9jC1HFmbf7hQ3SNHPE6sC', 'Etudiant'),
('Kaouthar-El', 'kaouthar', 'Elallam', '$2y$10$uJ6EfGRJsw9ZpaKjGXjhPOKEAviUGnChJID0Q4Lu8N6EiOnXfFv5K', 'Etudiant'),
('kenza_Mam', 'benMa', 'Kenza', '$2y$10$NdQuSNeS2KNyQVy3cf0SHe4wEgnFfh/tZZSrf3qM2AaWHtsxQWwti', 'Professeur'),
('khadija-El', 'El ali', 'khadija', '$2y$10$vbr3zabKbz8qkxzlpZNKBOn2BMFZx5tTCJ8e/L.rM8hZCrUDPYQRS', 'Professeur'),
('noura_El', 'bour', 'noura', '$2y$10$01xQiDFdtiwKB2KjLY4E0.nZFSRpy0yeaVS5B5KJ8HUV9/FaFHvnm', 'Etudiant'),
('test', 'test', 'test', 'test', 'Professeur');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `annonce_comments`
--
ALTER TABLE `annonce_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annonce_id` (`annonce_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_cours` (`code_cours`);

--
-- Indexes for table `devoir`
--
ALTER TABLE `devoir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cours_id` (`cours_id`),
  ADD KEY `professeur_id` (`professeur_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id_materiel`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `soumissions_devoir`
--
ALTER TABLE `soumissions_devoir`
  ADD PRIMARY KEY (`soumission_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `devoir_id` (`devoir_id`);

--
-- Indexes for table `student_course_access`
--
ALTER TABLE `student_course_access`
  ADD PRIMARY KEY (`student_id`,`course_code`),
  ADD KEY `course_code` (`course_code`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`utilisateur_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `annonce_comments`
--
ALTER TABLE `annonce_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `devoir`
--
ALTER TABLE `devoir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id_materiel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `soumissions_devoir`
--
ALTER TABLE `soumissions_devoir`
  MODIFY `soumission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annonce_comments`
--
ALTER TABLE `annonce_comments`
  ADD CONSTRAINT `annonce_comments_ibfk_1` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`),
  ADD CONSTRAINT `annonce_comments_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

--
-- Constraints for table `devoir`
--
ALTER TABLE `devoir`
  ADD CONSTRAINT `devoir_ibfk_1` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`),
  ADD CONSTRAINT `devoir_ibfk_2` FOREIGN KEY (`professeur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `soumissions_devoir`
--
ALTER TABLE `soumissions_devoir`
  ADD CONSTRAINT `soumissions_devoir_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`),
  ADD CONSTRAINT `soumissions_devoir_ibfk_2` FOREIGN KEY (`devoir_id`) REFERENCES `devoir` (`id`);

--
-- Constraints for table `student_course_access`
--
ALTER TABLE `student_course_access`
  ADD CONSTRAINT `student_course_access_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `utilisateur` (`utilisateur_id`),
  ADD CONSTRAINT `student_course_access_ibfk_2` FOREIGN KEY (`course_code`) REFERENCES `cours` (`code_cours`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
