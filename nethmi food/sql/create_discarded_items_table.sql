-- SQL to create discarded_items table

CREATE TABLE IF NOT EXISTS `discarded_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(50) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity_discarded` decimal(10,2) NOT NULL,
  `discard_reason` varchar(255) NOT NULL,
  `discard_date` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `discard_date` (`discard_date`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
