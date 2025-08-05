CREATE TABLE IF NOT EXISTS `user_trail_favorites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `trail_id` INT NOT NULL,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (`user_id`, `trail_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`trail_id`) REFERENCES `IT202-M25-Trails`(`id`) ON DELETE CASCADE
);