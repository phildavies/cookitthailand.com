CREATE TABLE IF NOT EXISTS `#__route66_ai_tools` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `temperature` float DEFAULT NULL,
  `state` tinyint NOT NULL DEFAULT 0,
  `target` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `core` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned DEFAULT NULL,
  `checked_out` int unsigned DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_alias` (`alias`),
  KEY `idx_ordering` (`ordering`),
  KEY `idx_core` (`core`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_content_analysis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint unsigned DEFAULT NULL,
  `link_hash` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keyphrase` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_score` tinyint NOT NULL DEFAULT 0,
  `readability_score` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_page_id` (`page_id`),
  UNIQUE KEY `idx_link_hash` (`link_hash`),
  UNIQUE KEY `idx_resource_id` (`resource_id`),
  KEY `idx_seo_score` (`seo_score`),
  KEY `idx_readability_score` (`readability_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_crawler_queue` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_link_hash` (`link_hash`),
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_crawler_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `queue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_metadata` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint unsigned DEFAULT NULL,
  `link_hash` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `robots` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `x_title` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `x_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `x_image` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_page_id` (`page_id`),
  UNIQUE KEY `idx_link_hash` (`link_hash`),
  UNIQUE KEY `idx_resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_hash` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hash` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_length` smallint unsigned NOT NULL DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_hash` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_length` smallint unsigned NOT NULL DEFAULT 0,
  `duplicate_title` tinyint DEFAULT NULL,
  `duplicate_description` tinyint DEFAULT NULL,
  `duplicate_resource` tinyint DEFAULT NULL,
  `http_status` smallint unsigned NOT NULL,
  `redirect_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` int NOT NULL,
  `time` int NOT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` bigint unsigned DEFAULT NULL,
  `resource_id` varchar(100) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (concat(`component`, '.', `view`, '.', `key`)) VIRTUAL,
  `crawled` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `language` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robots` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dom_nodes` mediumint unsigned NOT NULL DEFAULT 0,
  `content_encoding` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_hash` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_index` tinyint(1) DEFAULT 0,
  `no_follow` tinyint(1) DEFAULT 0,
  `robots_txt_blocked` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_link_hash` (`link_hash`),
  KEY `idx_title_hash` (`title_hash`),
  KEY `idx_description_hash` (`description_hash`),
  KEY `idx_crawled` (`crawled`),
  KEY `idx_size` (`size`),
  KEY `idx_time` (`time`),
  KEY `idx_dom_nodes` (`dom_nodes`),
  KEY `idx_content_encoding` (`content_encoding`),
  KEY `idx_http_status` (`http_status`),
  KEY `idx_duplicate_title` (`duplicate_title`),
  KEY `idx_duplicate_description` (`duplicate_description`),
  KEY `idx_duplicate_resource` (`duplicate_resource`),
  KEY `idx_no_index` (`no_index`),
  KEY `idx_no_follow` (`no_follow`),
  KEY `idx_robots_txt_blocked` (`robots_txt_blocked`),
  KEY `idx_resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_robots_txt` (
  `id` tinyint(1) NOT NULL DEFAULT 1,
  `contents` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__route66_sitemaps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `sources` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`)
VALUES ('Route 66 robots.txt', 'com_route66.robots', '{\"special\":{\"dbtable\":\"#__route66_robots_txt\",\"key\":\"id\",\"type\":\"RobotsTable\",\"prefix\":\"Firecoders\\\\Component\\\\Route66\\\\Administrator\\\\Table\\\\\",\"config\":\"array()\"}', '', '', '', '{\"formFile\":\"administrator\\/components\\/com_route66\\/forms\\/robots.xml\", \"hideFields\":[\"id\"], \"ignoreChanges\":[], \"convertToInt\":[], \"displayLookup\":[]}');

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`)
VALUES ('Route 66 AI Tool', 'com_route66.aitool', '{\"special\":{\"dbtable\":\"#__route66_ai_tools\",\"key\":\"id\",\"type\":\"AIToolTable\",\"prefix\":\"Firecoders\\\\Component\\\\Route66\\\\Administrator\\\\Table\\\\\",\"config\":\"array()\"}', '', '', '', '{\"formFile\":\"administrator\\/components\\/com_route66\\/forms\\/aitool.xml\", \"hideFields\":[\"id\",\"checked_out\",\"checked_out_time\", \"alias\", \"state\", \"created\", \"created_by\", \"ordering\", \"modified_by\", \"modified\", \"core\"], \"ignoreChanges\":[\"modified_by\", \"modified\", \"checked_out\", \"checked_out_time\", \"created\", \"created_by\", \"ordering\", \"core\"], \"convertToInt\":[], \"displayLookup\":[{\"sourceColumn\":\"modified_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}]}');

INSERT INTO `#__route66_ai_tools` 
(`title`, `alias`, `description`, `prompt`, `instructions`, `temperature`, `state`, `target`, `core`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `ordering`)
VALUES
('SEO Title', 'seo-title', 'Creates a short and SEO-optimized title that boosts visibility and clicks.', 'Generate an SEO-friendly meta title for a web page based on the following article. Keep the title under 60 characters. Use the keyphrase naturally if appropriate.\r\n\r\nYour response should only contain the generated title.\r\n\r\nKeyphrase: {keyphrase}\r\nLanguage: {language}\r\n\r\nArticle title:\r\n{title}\r\n\r\nArticle content:\r\n{text}', 'You are an expert SEO assistant. Write short, compelling meta titles that encourage clicks and are optimized for search engines.', 0.7, 1, 'seo_title', 1, NOW(), 0, NULL, NULL, NULL, NULL, 1),

('Meta Description', 'meta-description', 'Writes a compelling meta description between 140–160 characters to increase CTR.', 'Write a meta description for the following article, using natural language and including the keyphrase if appropriate. Keep it between 140 and 160 characters.\r\n\r\nYour response should only contain the generated description.\r\n\r\nKeyphrase: {keyphrase}\r\nLanguage: {language}\r\n\r\nArticle title:\r\n{title}\r\n\r\nContent:\r\n{text}', 'You specialize in writing SEO-optimized meta descriptions that summarize the page clearly and increase click-through rate.', 0.7, 1, 'meta_description', 1, NOW(), 0, NULL, NULL, NULL, NULL, 2),

('Open Graph Title', 'open-graph-title', 'Generates an engaging title for Open Graph tags to improve link previews on social media.', 'Generate a catchy and concise Open Graph title (under 60 characters) for social media sharing. Focus on making it attention-grabbing and relevant to the article.\r\n\r\nYour response should only contain the generated Open Graph title.\r\n\r\nKeyphrase: {keyphrase}\r\nLanguage: {language}\r\n\r\nArticle title:\r\n{title}\r\n\r\nContent:\r\n{text}', 'You write social sharing titles for platforms like Facebook and LinkedIn. Make them engaging and relevant to the article’s content.', 0.7, 1, 'og_title', 1, NOW(), 0, NULL, NULL, NULL, NULL, 3),

('Open Graph Description', 'open-graph-description', 'Crafts short, click-worthy descriptions for Open Graph metadata (around 100–110 characters).', 'Write an Open Graph description (about 100–110 characters) to be used when this article is shared on social media. It should attract clicks and reflect the article\'s value.\r\n\r\nYour response should only contain the generated Open Graph description.\r\n\r\nKeyphrase: {keyphrase}\r\nLanguage: {language}\r\n\r\nArticle title:\r\n{title}\r\n\r\nArticle Content:\r\n{text}', 'You are a social content strategist. Write short, punchy Open Graph descriptions that boost engagement on platforms like Facebook.', 0.7, 1, 'og_description', 1, NOW(), 0, NULL, NULL, NULL, NULL, 4),

('X/Twitter Title', 'x-twitter-title', 'Creates a concise, scroll-stopping headline for X (formerly Twitter).', 'Create a short and scroll-stopping headline (max 60 characters) for this article to be shared on X (formerly Twitter). Emphasize clarity and appeal.\r\n\r\nYour response should only contain the generated Twitter/X title.\r\n\r\nKeyphrase: {keyphrase}\r\nLanguage: {language}\r\n\r\nArticle title:\r\n{title}\r\n\r\nArticle Content:\r\n{text}', 'You are a social media copywriter. Craft headlines that perform well on fast-scrolling platforms like X.', 0.7, 1, 'x_title', 1, NOW(), 0, NULL, NULL, NULL, NULL, 5),

('X/Twitter Description', 'x-twitter-description', 'Creates a tweet-style summary for X, under 110 characters, with a casual and engaging tone.', 'Write a casual, tweet-style description (under 110 characters) that summarizes this article and encourages clicks on X (Twitter). Add some personality if appropriate.\r\n\r\nYour response should only contain the generated Twitter/X description.\r\n\r\nKeyphrase: {keyphrase}\r\nLanguage: {language}\r\n\r\nArticle title:\r\n{title}\r\n\r\nArticle Content:\r\n{text}', 'You write short, engaging text for social platforms. Keep it informal and reader-focused.', 0.7, 1, 'x_description', 1, NOW(), 0, NULL, NULL, NULL, NULL, 6),

('Rewrite Text', 'rewrite-text', 'Refines and rewrites selected content to improve clarity, tone, and grammar.', 'Rewrite the following text to improve tone, clarity and grammar:\r\n\r\n{text}', 'You are a writing assistant that rewrites text to be clearer, more concise, and grammatically correct.', 0.7, 1, 'text', 1, NOW(), 0, NULL, NULL, NULL, NULL, 7),

('Summarize', 'summarize', 'Condenses content into a clear and concise summary paragraph.', 'Summarize the following text in 1-2 sentences:\r\n\r\n{text}', 'You are a summarization assistant. Provide clear, concise summaries suitable for general audiences.', 0.7, 1, 'text', 1, NOW(), 0, NULL, NULL, NULL, NULL, 8),

('Continue Writing', 'continue-writing', 'Expands the provided text with relevant content in the same tone and structure.', 'Continue writing from the following content in the same tone and topic:\r\n\r\n{text}', 'You are a creative writing assistant. Extend the provided content smoothly and logically. Use semantic HTML markup that would be injected inside a WYSIWYG editor. Use headings where required. Do not include markdown in your response.', 0.7, 1, 'text', 1, NOW(), 0, NULL, NULL, NULL, NULL, 9),

('Command', 'command', 'Executes the user-provided text as a prompt.', '{text}', 'You are an experienced copywriter.', 0.7, 1, 'text', 1, NOW(), 0, NULL, NULL, NULL, NULL, 10);