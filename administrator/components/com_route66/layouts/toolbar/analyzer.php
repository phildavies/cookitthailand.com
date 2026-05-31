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

$hasResourceId = $form->getValue('resource_id', 'analysis') || $form->getValue('resource_id', 'metadata');

$wa = Factory::getDocument()->getWebAssetManager();
$wa->useStyle('route66-analyzer');
$wa->useStyle('route66-toolbar');
$wa->useScript('route66-analyzer');
$wa->useScript('route66-fields-cloner');

?>

<?php if ($hasResourceId): ?>
<script type="module" data-cfasync="false">
import Route66Analyzer from 'route66-analyzer';
import Route66FormFieldCloner from 'route66-fields-cloner';
document.addEventListener('DOMContentLoaded', () => {
    new Route66Analyzer();
    new Route66FormFieldCloner('#route66-analyzer-dropdown form', 'form[method]');
});
</script>
<?php endif; ?>

<joomla-toolbar-button id="route66-analyzer-button">
    <div class="dropdown">
        <button class="btn btn-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
            <span class="route66-badge route66-seo-badge" aria-hidden="true"><i class="fa-solid fa-clock"></i></span>
            <?php echo Text::_('COM_ROUTE66_SEO'); ?>
        </button>
        <div class="dropdown-menu" id="route66-analyzer-dropdown">
            <div class="main-card">
                <?php if (!$hasResourceId): ?>
                <?php echo LayoutHelper::render('analyzer.warning', [], JPATH_SITE . '/administrator/components/com_route66/layouts'); ?>
                <?php else: ?>
                <form class="form form-vertical">
                    <?php echo HTMLHelper::_('uitab.startTabSet', 'route66Tabs', ['active' => 'seo']); ?>
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
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</joomla-toolbar-button>