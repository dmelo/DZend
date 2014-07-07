create table `ta` (
    `id` int(11) auto_increment NOT NULL,
    `name` varchar(31) NOT NULL,
    `group` enum('g1', 'g2', 'g3'),
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `ta_created_trigger` BEFORE INSERT ON `ta` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
