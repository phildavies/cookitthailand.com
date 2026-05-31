ALTER TABLE `#__aimysitemap`
    MODIFY `lang`
    char(5) NOT NULL DEFAULT '*';

ALTER TABLE `#__aimysitemap`
    MODIFY `state`
    bool NOT NULL DEFAULT 0;

ALTER TABLE `#__aimysitemap`
    MODIFY `lock`
    bool NOT NULL DEFAULT 0;

ALTER TABLE `#__aimysitemap_crawl`
    MODIFY `lang`
    char(5) NOT NULL DEFAULT '*';

ALTER TABLE `#__aimysitemap_crawl`
    MODIFY `crawled`
    bool NOT NULL DEFAULT 0;

ALTER TABLE `#__aimysitemap_crawl`
    MODIFY `index`
    bool NOT NULL DEFAULT 0;
