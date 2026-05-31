ALTER TABLE `#__route66_seo` ADD COLUMN `readability` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__route66_seo` ADD INDEX `readability` (`readability`);
