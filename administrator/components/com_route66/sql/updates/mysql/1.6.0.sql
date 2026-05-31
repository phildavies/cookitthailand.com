CREATE TABLE IF NOT EXISTS `#__route66_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `context` varchar(255) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `context` (`context`),
  KEY `resourceId` (`resourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
