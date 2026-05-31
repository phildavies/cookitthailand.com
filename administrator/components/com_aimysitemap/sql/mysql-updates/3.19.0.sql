ALTER TABLE `#__aimysitemap_crawl`
    ADD COLUMN `code`
    smallint NOT NULL DEFAULT 0
    AFTER `url`;
ALTER TABLE `#__aimysitemap_crawl`
    ADD COLUMN `refs`
    mediumtext NOT NULL DEFAULT ''
    AFTER `lang`;

CREATE TABLE IF NOT EXISTS `#__aimysitemap_broken_links` (
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `url`        varbinary(767)   NOT NULL UNIQUE,
    `srcs`       mediumtext       NOT NULL DEFAULT '',
    PRIMARY KEY( `id` )
) DEFAULT CHARSET=utf8;
