-- phpMyAdmin SQL Dump
-- version 5.2.2deb1+deb13u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2026 at 09:52 AM
-- Server version: 11.8.6-MariaDB-0+deb13u1 from Debian
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `username`, `email`, `avatar_path`, `password_hash`, `created_at`, `updated_at`, `status`, `notification_count`) VALUES
(1, 'CamilleClubLit', 'CamilleClubLit@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(2, 'Alexlecture', 'Alexlecture@mail.com', '/upload/avatars/Alexlecture.jpg', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2025-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(3, 'nathalire', 'nathalie@mail.com', '/upload/avatars/nathalire.jpg', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(4, 'Hugo1990_12', 'Hugo1990_12@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(5, 'Juju1432', 'Juju1432@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(6, 'Christiane75014', 'Christiane75014@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(7, 'Hamzalecture', 'Hamzalecture@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(8, 'Lou&Ben50', 'Lou&Ben50@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(9, 'Lolobzh', 'Lolobzh@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(10, 'Sas634', 'Sas634@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(11, 'ML95', 'ML95@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(12, 'Verogo33', 'Verogo33@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(13, 'AnnikaBrahms', 'AnnikaBrahms@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(14, 'Victoirefabr912', 'Victoirefabr912@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(15, 'Lotrfanclub67', 'Lotrfanclub67@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0);


-- --------------------------------------------------------
--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `image_path`, `description`, `availability`, `fk_member_id`) VALUES
(1, 'Esther', 'Alabaster', '/upload/books/Esther.jpg', 'Description1', 'AVAILABLE', 1),
(2, 'The Kinfolk Table', 'Nathan Williams', '/upload/books/The Kinfolk Table.jpg', 'J\'ai récemment plongé dans les pages de \'The Kinfolk Table\' et j\'ai été enchanté par cette œuvre captivante. Ce livre va bien au-delà d\'une simple collection de recettes ; il célèbre l\'art de partager des moments authentiques autour de la table.<br><br>Les photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des recettes et des histoires qui mettent en avant la beauté de la simplicité et de la convivialité.<br><br>Chaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers.<br><br>\'The Kinfolk Table\' incarne parfaitement l\'esprit de la cuisine et de la camaraderie, et il est certain que ce livre trouvera une place spéciale dans le cœur de tout amoureux de la cuisine et des rencontres inspirantes.', 'AVAILABLE', 2),
(3, 'Wabi Sabi', 'Beth Kempton', '/upload/books/Wabi Sabi.jpg', 'Description3', 'AVAILABLE', 2),
(4, 'Milk & honey', 'Rupi Kaur', '/upload/books/Milk & honey.jpg', 'Description4', 'AVAILABLE', 4),
(5, 'Delight!', 'Justin Rossow', '/upload/books/Delight.jpg', 'Description5', 'AVAILABLE', 5),
(6, 'Milwaukee Mission', 'Elder Cooper Low', '/upload/books/Milwaukee Mission.jpg', 'Description1', 'AVAILABLE', 6),
(7, 'Minimalist Graphics', 'Julia Schonlau', '/upload/books/Minimalist Graphics.jpg', 'Description2', 'AVAILABLE', 7),
(8, 'Hygge', 'Meik Wiking', '/upload/books/Hygge.jpg', 'Description3', 'AVAILABLE', 4),
(9, 'Innovation', 'Matt Ridley', '/upload/books/Innovation.jpg', 'Description4', 'AVAILABLE', 8),
(10, 'Psalms', 'Alabaster', '/upload/books/Psalms.jpg', 'Description5', 'AVAILABLE', 9),
(11, 'Thinking, Fast & Slow', 'Daniel Kahneman', '/upload/books/Thinking, Fast & Slow.jpg', 'Description1', 'AVAILABLE', 10),
(12, 'A Book Full Of Hope', 'Rupi Kaur', '/upload/books/A Book Full Of Hope.jpg', 'Description2', 'AVAILABLE', 11),
(13, 'The Subtle Art Of...', 'Mark Manson', '/upload/books/The Subtle Art Of....jpg', 'Description3', 'AVAILABLE', 12),
(14, 'Narnia', 'C.S Lewis', '/upload/books/Narnia.jpg', 'Description4', 'AVAILABLE', 13),
(15, 'Company Of One', 'Paul Jarvis', '/upload/books/Company Of One.jpg', 'Description5', 'AVAILABLE', 14),
(16, 'The Two Towers', 'J.R.R Tolkien', '/upload/books/The Two Towers.jpg', 'Description1', 'AVAILABLE', 15);

-- --------------------------------------------------------


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
