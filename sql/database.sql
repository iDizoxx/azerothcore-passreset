-- Table structure for table `password_resets`

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `password_resets`

LOCK TABLES `password_resets` WRITE;

-- Disable keys for faster insert (optional)
ALTER TABLE `password_resets` DISABLE KEYS;

-- Insert data here (values would be added in this section)
-- INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES ...

-- Re-enable keys after inserting data
ALTER TABLE `password_resets` ENABLE KEYS;

UNLOCK TABLES;
