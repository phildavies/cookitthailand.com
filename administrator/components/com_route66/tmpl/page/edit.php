<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Analyzer\Analyzer;
use Firecoders\Component\Route66\Administrator\Helper\IssuesHelper;
use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$analyzer = new Analyzer(['item' => $this->item]);

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive');
$wa->useScript('form.validate');
$wa->useStyle('route66-analyzer');
$wa->useScript('route66-analyzer');
$wa->useScript('route66-ai');
//$wa->useScript('route66-readability');

Text::script('COM_ROUTE66_COULD_NOT_FETCH_PAGE_CONTENTS');

?>

<script type="module" data-cfasync="false">
import Route66Analyzer from 'route66-analyzer';

window.addEventListener('DOMContentLoaded', async () => {

    try {

        const response = await fetch('index.php?option=com_route66&task=page.get&id=<?php echo $this->item->id; ?>&format=raw', {
            method: 'GET',
        });

        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        document.getElementById('jform_text').value = doc.querySelector('body').innerHTML;
        new Route66Analyzer();

    } catch (error) {
        Joomla.renderMessages({'warning': [Joomla.Text._('COM_ROUTE66_COULD_NOT_FETCH_PAGE_CONTENTS')]});
    }
});

</script>

<form action="<?php echo Route::_('index.php?option=com_route66&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="page-form" aria-label="<?php echo Text::_('COM_ROUTE66_PAGE_EDIT'); ?>" class="form form-vertical form-validate">

    <?php echo $this->form->renderField('id'); ?>
    <?php echo $this->form->renderField('title'); ?>
    <?php echo $this->form->renderField('text'); ?>
    <?php echo $this->form->renderField('description'); ?>
    <?php echo $this->form->renderField('language'); ?>

    <div class="main-card">

        <?php echo HTMLHelper::_('uitab.startTabSet', 'route66Tabs', ['active' => 'info']); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'info', Text::_('COM_ROUTE66_CRAWL_REPORT')); ?>
        <?php echo LayoutHelper::render('page.info', ['item' => $this->item, 'issues' => IssuesHelper::check($this->item)]); ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'seo', Text::_('COM_ROUTE66_SEO_BADGE')); ?>
        <?php echo LayoutHelper::render('page.seo', ['form' => $this->form]); ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'readability', Text::_('COM_ROUTE66_READABILITY_BADGE')); ?>
        <?php echo LayoutHelper::render('page.readability', ['form' => $this->form]); ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'search-engines', Text::_('COM_ROUTE66_SEARCH_ENGINES')); ?>
        <?php echo LayoutHelper::render('page.metadata', ['form' => $this->form]); ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'route66Tabs', 'social', Text::_('COM_ROUTE66_SOCIAL')); ?>
        <?php echo LayoutHelper::render('page.social', ['form' => $this->form]); ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>
    <?php echo $this->form->renderControlFields(); ?>
</form>
<?php echo Route66Helper::copyrights(); ?>
