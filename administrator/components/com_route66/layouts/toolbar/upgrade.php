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

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('core')->useScript('webcomponent.toolbar-button');


Text::script('COM_ROUTE66_PRO_FEATURE');
?>
<joomla-toolbar-button>
    <button class="btn btn-primary" type="button" onclick="Joomla.renderMessages({'info': [Joomla.Text._('COM_ROUTE66_PRO_FEATURE')]}); return false;">
        <span class="<?php echo $icon; ?>" aria-hidden="true"></span>
        <?php echo Text::_($title); ?>
    </button>
</joomla-toolbar-button>