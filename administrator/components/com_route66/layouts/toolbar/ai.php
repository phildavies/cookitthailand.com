<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

extract($displayData);

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

$wa->useScript('core')
    ->useScript('joomla.dialog-autocreate')
    ->useScript('webcomponent.toolbar-button');


$options = [
    'popupType'  => 'iframe',
    'className'  => 'route66-ai-tools',
    'src'        => 'index.php?option=com_route66&view=aitools&filter[state]=1&list[fullordering]=aitool.ordering+ASC&layout=modal&tmpl=component&'.Session::getFormToken().'=1',
    'textHeader' => Text::_('COM_ROUTE66_AI_TOOLS'),
];

?>
<joomla-toolbar-button id="route66-ai-button">
    <button class="btn btn-primary" data-joomla-dialog="<?php echo $this->escape(json_encode($options, JSON_UNESCAPED_SLASHES)); ?>" type="button">
        <span class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></span>
        <?php echo Text::_('COM_ROUTE66_AI_TOOLS'); ?>
    </button>
</joomla-toolbar-button>