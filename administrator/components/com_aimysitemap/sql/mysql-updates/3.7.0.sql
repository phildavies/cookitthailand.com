CREATE TABLE IF NOT EXISTS `#__aimysitemap_crawl` (
    `url`        varchar(255)     NOT NULL UNIQUE
                                  COMMENT 'Crawled URL',
    `crawled`    bool             DEFAULT FALSE
                                  COMMENT 'Crawled already?',
    `index`      bool             DEFAULT FALSE
                                  COMMENT 'Index URL?',
    `title`      varchar(255)     NOT NULL DEFAULT '',
    `mtime`      bigint           NOT NULL DEFAULT 0,
    PRIMARY KEY( `url` )
) DEFAULT CHARSET=utf8;

ALTER TABLE `#__aimysitemap`
    MODIFY `title`
    varchar(255) NOT NULL DEFAULT '';
