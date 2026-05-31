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
$wa->addInlineStyle('#fieldset-route66 { border: none}');

?>
<div class="alert alert-info m-1" role="alert"><?php echo Text::_('COM_ROUTE66_SAVE_ONCE_WARNING'); ?></div>
