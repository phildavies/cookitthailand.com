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
$wa->useScript('keepalive');
$wa->useScript('form.validate');

?>

<form action="<?php echo Route::_('index.php?option=com_route66&view=robots&layout=edit&id='.$this->item->id); ?>" method="post" name="adminForm" id="robots-form" aria-label="<?php echo Text::_('COM_ROUTE66_EDIT_ROBOTS_TXT'); ?>" class="form form-vertical form-validate">

    <?php echo $this->form->renderField('id'); ?>

    <div class="main-card">
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <fieldset class="adminform p-4">
                        <?php echo $this->form->renderField('contents'); ?>
                        <?php echo $this->form->renderField('version_note'); ?>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->form->renderControlFields(); ?>
</form>
<?php echo Route66Helper::copyrights(); ?>
