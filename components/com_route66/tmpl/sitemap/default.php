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
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" <?php if ($this->item->settings->get('images')): ?>xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"<?php endif; ?> <?php if ($this->item->settings->get('videos')): ?>xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"<?php endif; ?>>
	<?php foreach ($this->items as $item): ?>
	<url>
		<loc><?php echo $this->escape($item->url); ?></loc>
		<?php if ($item->modifiedDate): ?>
		<lastmod><?php echo $this->escape($item->modifiedDate); ?></lastmod>
		<?php endif; ?>
		<?php foreach ($item->images as $image): ?>
		<image:image>
			 <image:loc><?php echo $this->escape($image->url); ?></image:loc>
			 <?php if (isset($image->caption) && $image->caption): ?>
			 <image:caption><?php echo htmlspecialchars($image->caption, ENT_QUOTES, 'UTF-8'); ?></image:caption>
			 <?php endif; ?>
		</image:image>
		<?php endforeach; ?>
		<?php foreach ($item->videos as $video): ?>
		<video:video>
			<?php if ($video->thumbnail): ?>
			<video:thumbnail_loc><?php echo $this->escape($video->thumbnail); ?></video:thumbnail_loc>
			<?php endif; ?>
			<?php if ($video->title): ?>
			<video:title><?php echo htmlspecialchars($video->title, ENT_QUOTES, 'UTF-8'); ?></video:title>
			<?php endif; ?>
			<?php if ($video->description): ?>
			<video:description><?php echo htmlspecialchars($video->description, ENT_QUOTES, 'UTF-8'); ?></video:description>
			<?php endif; ?>
			<?php if ($video->location): ?>
			<video:content_loc><?php echo $this->escape($video->location); ?></video:content_loc>
			<?php endif; ?>
			<?php if ($video->player): ?>
			<video:player_loc allow_embed="yes" autoplay="ap=1"><?php echo $this->escape($video->player); ?></video:player_loc>
			<?php endif; ?>
		</video:video>
		<?php endforeach; ?>
	</url>
	<?php endforeach; ?>
</urlset>
