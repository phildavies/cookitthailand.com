ALTER TABLE `#__aimysitemap`
    ADD COLUMN `lang`
    char(7) NOT NULL DEFAULT '*'
    AFTER `mtime`;

ALTER TABLE `#__aimysitemap`
    ADD COLUMN `lock`
    bool NOT NULL DEFAULT false
    AFTER `state`;

ALTER TABLE `#__aimysitemap_crawl`
    ADD COLUMN `lang`
    char(7) NOT NULL DEFAULT '*'
    AFTER `mtime`;
