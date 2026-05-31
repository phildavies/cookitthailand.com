ALTER TABLE `#__aimysitemap`
    MODIFY `url`
    varbinary(767) NOT NULL;
ALTER TABLE `#__aimysitemap_crawl`
    MODIFY `url`
    varbinary(767) NOT NULL;
