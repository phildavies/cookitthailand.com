CREATE TABLE IF NOT EXISTS `#__route66_sitemaps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `sources` text NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__route66_instant_articles_feeds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `sources` text NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__route66_seo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `context` varchar(255) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `score` tinyint(1) unsigned NOT NULL,
  `readability` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `context` (`context`),
  KEY `resourceId` (`resourceId`),
  KEY `score` (`score`),
  KEY `readability` (`readability`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__route66_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `context` varchar(255) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `context` (`context`),
  KEY `resourceId` (`resourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
