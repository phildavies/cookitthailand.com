<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\IssuesHelper;
use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_route66&view=pages&layout=duplicates&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                    <table class="table table-responsive" id="pagesList">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <?php echo Text::_('JGLOBAL_TITLE'); ?>
                                </th>
                                 <th scope="col">
                                    <?php echo Text::_('COM_ROUTE66_ISSUES'); ?>
                                </th>
                                <th scope="col" class="w-20 text-center">
                                    <?php echo Text::_('COM_ROUTE66_LAST_CRAWLED'); ?>
                                </th>
                                <th scope="col" class="w-5 text-center">
                                    <?php echo Text::_('JGRID_HEADING_ID'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td>
                                        <a target="_blank" href="<?php echo $item->editLink; ?>"><?php echo $this->escape($item->title); ?></a>
                                        <div class="small"><a target="_blank" href="<?php echo $item->url; ?>"><?php echo $item->link; ?></a></div>
                                    </td>
                                    <td>
                                        <?php foreach (IssuesHelper::check($item) as $issue): ?>
                                            <span class="badge text-bg-<?php echo $issue->type; ?>"><?php echo $issue->label; ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="small text-center">
                                        <?php echo HTMLHelper::_('date', $item->crawled, Text::_('DATE_FORMAT_LC2')); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>

                <?php echo $this->filterForm->renderControlFields(); ?>
            </div>
        </div>
    </div>

    <?php echo Route66Helper::copyrights(); ?>
</form>
