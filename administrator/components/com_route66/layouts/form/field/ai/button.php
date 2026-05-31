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
$wa->addInlineStyle('.form-vertical .route66-ai-tool-button { top: -30px;}');
?>
<a class="route66-ai-tool-button btn-sm btn-link fs-6 position-absolute end-0" role="button" data-ai-tool-id="<?php echo $tool->id; ?>" data-ai-tool-target="<?php echo $tool->target; ?>">
    <i class="fa-solid fa-wand-magic-sparkles"></i> 
    <?php echo Text::_('COM_ROUTE66_USE_AI'); ?>
</a>
