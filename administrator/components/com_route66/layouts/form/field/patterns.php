<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

extract($displayData);

$form = $forms[0];

?>

<div class="subform-wrapper">
    <details>
        <summary>
            <?php echo Text::_('COM_ROUTE66_PATTERNS_HELP_TITLE'); ?>
        </summary>
        <div><?php echo Text::_('COM_ROUTE66_PATTERNS_HELP'); ?></div>
    </details>
    <joomla-tab id="route66-patterns-tabs" recall breakpoint="728">
    <?php foreach ($form->getFieldsets() as $fieldset) : ?>
        <joomla-tab-element class="tab-pane" name="<?php echo Text::_($fieldset->label); ?>" id="route66-pattern-<?php echo $fieldset->name; ?>"><?php echo $form->renderFieldset($fieldset->name); ?></joomla-tab-element>
    <?php endforeach; ?>
    </joomla-tab>
</div>