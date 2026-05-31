<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
?>

<form action="<?php echo Route::_('index.php?option=com_route66&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="page-form" aria-label="<?php echo Text::_('COM_ROUTE66_SITEMAP_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate form-vertical">
<div class="main-card p-4">
    <div class="row">
        <div class="col-lg-9">
            <?php echo $this->form->renderField('title'); ?>
            <?php foreach ($this->form->getGroup('sources') as $field) : ?>
                <?php echo $field->renderField(); ?>
            <?php endforeach; ?>
            <div class="border rounded bg-light-subtle p-3 mt-4">
                <small class="text-muted"><?php echo Route66Helper::isPro() ? Text::_('COM_ROUTE66_REQUEST_INTEGRATION') : Text::_('COM_ROUTE66_SITEMAPS_PRO_NOTE'); ?></small>
            </div>
        </div>
        <div class="col-lg-3">
            <?php echo $this->form->renderField('state'); ?>
            <?php foreach ($this->form->getGroup('settings') as $field) : ?>
                <?php echo $field->renderField(); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
    <?php echo $this->form->renderControlFields(); ?>
</form>
<?php echo Route66Helper::copyrights(); ?>
