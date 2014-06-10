CREATE TABLE IF NOT EXISTS `civicrm_tag_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11),
  `coordinator_id` int(11),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `TAG_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;