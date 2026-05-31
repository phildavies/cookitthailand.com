<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

extract($displayData);

$wa = Factory::getDocument()->getWebAssetManager();
$wa->useStyle('route66-search-preview');
$wa->useScript('route66-ai');

?>

<script type="module" data-cfasync="false">
import Route66AI from 'route66-ai';
document.addEventListener('DOMContentLoaded', () => {
    new Route66AI();
});
</script>

<div class="route66-search-preview">
    <div class="route66-search-preview-title">Firecoders: Premium & Free Joomla Extensions</div>
    <div class="route66-search-preview-url">https://www.firecoders.com</div>
    <div class="route66-search-preview-description">We build the most elegant, modern and easy to use Joomla extensions</div>
</div>
<div class="small text-muted mt-1 mb-4"><?php echo Text::_('COM_ROUTE66_SERP_DESC'); ?></div>

<?php echo $form->renderField('id', 'metadata'); ?>
<?php echo $form->renderField('resource_id', 'metadata'); ?>
<?php echo $form->renderField('link_hash', 'metadata'); ?>
<?php echo $form->renderField('title', 'metadata'); ?>
<?php echo $form->renderField('description', 'metadata'); ?>
<?php echo $form->renderField('robots', 'metadata'); ?>
<?php echo $form->renderField('canonical', 'metadata'); ?>