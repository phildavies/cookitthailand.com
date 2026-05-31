<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Button\PublishedButton;
use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_route66&view=sitemaps'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table" id="sitemapList">
                        <caption class="visually-hidden">
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'sitemap.state', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'sitemap.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'sitemap.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                     <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb'); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo (new PublishedButton())->render((int) $item->state, $i, ['task_prefix' => 'sitemaps.', 'id' => 'state-' . $item->id]); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $item->editLink; ?>"><?php echo $this->escape($item->title); ?></a>
                                        <div class="small"><a target="_blank" href="<?php echo $item->previewLink; ?>"><?php echo $item->previewLink; ?></a></div>
                                    </td>
                                    <td class="d-none d-md-table-cell text-center">
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>

                <?php endif; ?>

                <?php echo $this->filterForm->renderControlFields(); ?>
            </div>
        </div>
    </div>

    <?php echo Route66Helper::copyrights(); ?>
</form>
