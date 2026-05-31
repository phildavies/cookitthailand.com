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
