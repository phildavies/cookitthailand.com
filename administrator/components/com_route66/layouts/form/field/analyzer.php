<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

$wa = Factory::getDocument()->getWebAssetManager();
$wa->useStyle('route66-analyzer');
$wa->useScript('route66-analyzer');

$form          = $forms[0];
$hasResourceId = $form->getValue('resource_id', 'analysis') || $form->getValue('resource_id', 'metadata');

?>

<?php if ($hasResourceId): ?>
<script type="module" data-cfasync="false">
import Route66Analyzer from 'route66-analyzer';
document.addEventListener('DOMContentLoaded', () => {
    new Route66Analyzer();
});
</script>
<?php endif; ?>
<div class="subform-wrapper form-vertical">

    <?php if (!$hasResourceId): ?>
    <?php echo LayoutHelper::render('analyzer.warning', [], JPATH_SITE . '/administrator/components/com_route66/layouts'); ?>
    <?php else: ?>

    <?php echo HTMLHelper::_('uitab.startTabSet', 'route66Tabs', ['active' => 'seo', 'orientation' => 'horizontal']); ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'seo', Text::_('COM_ROUTE66_SEO_BADGE')); ?>
    <?php echo LayoutHelper::render('page.seo', ['form' => $form], JPATH_SITE . '/administrator/components/com_route66/layouts'); ?>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'readability', Text::_('COM_ROUTE66_READABILITY_BADGE')); ?>
    <?php echo LayoutHelper::render('page.readability', ['form' => $form], JPATH_SITE . '/administrator/components/com_route66/layouts'); ?>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'search-engines', Text::_('COM_ROUTE66_SEARCH_ENGINES')); ?>
    <?php echo LayoutHelper::render('page.metadata', ['form' => $form], JPATH_SITE . '/administrator/components/com_route66/layouts'); ?>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'social', Text::_('COM_ROUTE66_SOCIAL')); ?>
    <?php echo LayoutHelper::render('page.social', ['form' => $form], JPATH_SITE . '/administrator/components/com_route66/layouts'); ?>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <?php endif; ?>
</div>

