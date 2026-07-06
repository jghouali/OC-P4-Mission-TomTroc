DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `member_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COLLATE utf8mb4_uca1400_as_cs,
  `email` varchar(150) NOT NULL,
  `avatar_path` varchar(255) NOT NULL DEFAULT '/upload/avatars/default-avatar.png',
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('NOT-VALIDATED','VALIDATED') NOT NULL DEFAULT 'VALIDATED',
  `notification_count` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

CREATE TABLE `books` (
  `book_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `author` varchar(150) NOT NULL,
  `image_path` varchar(255) NOT NULL DEFAULT '/upload/books/default-book.png',
  `description` text NOT NULL,
  `availability` enum('NOT-AVAILABLE','AVAILABLE') NOT NULL DEFAULT 'AVAILABLE',
  `fk_member_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`book_id`),
  KEY `fk_member_id` (`fk_member_id`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`fk_member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

CREATE TABLE `messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fk_from_member_id` int(10) unsigned NOT NULL,
  `fk_to_member_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `fk_from_member_id` (`fk_from_member_id`),
  KEY `fk_to_member_id` (`fk_to_member_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`fk_from_member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`fk_to_member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
