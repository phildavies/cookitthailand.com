<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;
?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'.PHP_EOL; ?>
<?php echo '<?xml-stylesheet type="text/xsl" href="'.$this->xsl.'"?>'.PHP_EOL; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($this->items as $item): ?>
	<sitemap>
		<loc><?php echo $item; ?></loc>
	</sitemap>
	<?php endforeach; ?>
</sitemapindex>
