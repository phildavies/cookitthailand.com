ALTER TABLE `#__aimysitemap_crawl`
    MODIFY `refs` mediumtext NOT NULL;

ALTER TABLE `#__aimysitemap_broken_links`
    MODIFY `srcs` mediumtext NOT NULL;

ALTER TABLE `#__aimysitemap_kvstore`
    MODIFY `v` mediumtext NOT NULL COMMENT 'value';
