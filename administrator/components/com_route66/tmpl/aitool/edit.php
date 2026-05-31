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

<form action="<?php echo Route::_('index.php?option=com_route66&view=aitool&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="ai-tool-form" aria-label="<?php echo Text::_('COM_ROUTE66_AI_TOOL_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate form form-vertical">
    
    <?php echo $this->form->renderField('title'); ?>

    <div class="main-card p-4">
        <div class="row">
            <div class="col-lg-9">
                <?php echo $this->form->renderField('prompt'); ?>
                <?php echo $this->form->renderField('instructions'); ?>
                <?php echo $this->form->renderField('temperature'); ?>
                <?php echo $this->form->renderField('target'); ?>
            </div>
            <div class="col-lg-3">
                <?php echo $this->form->renderField('state'); ?>
                <?php echo $this->form->renderField('description'); ?>
                <?php echo $this->form->renderField('created_by'); ?>
                <?php echo $this->form->renderField('created'); ?>
                <?php echo $this->form->renderField('modified_by'); ?>
                <?php echo $this->form->renderField('modified'); ?>
                <?php echo $this->form->renderField('version_note'); ?>
            </div>
        </div>
    </div>
    <?php echo $this->form->renderField('alias'); ?>
    <?php echo $this->form->renderField('id'); ?>
    <?php echo $this->form->renderControlFields(); ?>
</form>
<?php echo Route66Helper::copyrights(); ?>
