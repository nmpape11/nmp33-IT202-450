CREATE TABLE `IT202-M25-Trails` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) UNIQUE NOT NULL,
  `state` VARCHAR(2) NOT NULL,
  `city` VARCHAR(255),
  `description` TEXT,
  `hiking` boolean DEFAULT FALSE,
  `mountain_biking` boolean DEFAULT FALSE,
  `camping` boolean DEFAULT FALSE,
  `caving` boolean DEFAULT FALSE,
  `trail_running` boolean DEFAULT FALSE,
  `snow_sports` boolean DEFAULT FALSE,
  `atv` boolean DEFAULT FALSE,
  `horseback_riding` boolean DEFAULT FALSE,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_api` tinyint(1) DEFAULT '1'
)