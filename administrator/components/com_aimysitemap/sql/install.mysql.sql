CREATE TABLE IF NOT EXISTS `#__aimysitemap` (
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `url`        varbinary(767)   NOT NULL UNIQUE,
    `title`      varchar(255)     NOT NULL DEFAULT '',
    `priority`   float            NOT NULL DEFAULT 0.5,
    `mtime`      bigint           NOT NULL DEFAULT 0,
    `lang`       char(5)          NOT NULL DEFAULT '*',
    `state`      bool             NOT NULL DEFAULT 0,
    `lock`       bool             NOT NULL DEFAULT 0,
    `changefreq` char(7)          NOT NULL DEFAULT 'monthly',
    PRIMARY KEY( `id` )
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__aimysitemap_crawl` (
    `url`        varbinary(767)   NOT NULL UNIQUE,
    `code`       smallint         NOT NULL DEFAULT 0,
    `crawled`    bool             NOT NULL DEFAULT 0,
    `index`      bool             NOT NULL DEFAULT 0,
    `title`      varchar(255)     NOT NULL DEFAULT '',
    `mtime`      bigint           NOT NULL DEFAULT 0,
    `lang`       char(5)          NOT NULL DEFAULT '*',
    `refs`       mediumtext       NOT NULL,
    PRIMARY KEY( `url` )
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__aimysitemap_kvstore` (
    `k`          varchar(64)      NOT NULL UNIQUE COMMENT 'key',
    `v`          longtext         NOT NULL        COMMENT 'value',
    PRIMARY KEY( `k` )
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__aimysitemap_broken_links` (
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `url`        varbinary(767)   NOT NULL UNIQUE,
    `srcs`       mediumtext       NOT NULL,
    PRIMARY KEY( `id` )
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- vim: sts=4 sw=4 ts=4 ai et ft=mysql:
