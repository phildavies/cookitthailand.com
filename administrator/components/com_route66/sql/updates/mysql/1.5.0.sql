CREATE TABLE IF NOT EXISTS `#__route66_seo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `context` varchar(255) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `score` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `context` (`context`),
  KEY `resourceId` (`resourceId`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
