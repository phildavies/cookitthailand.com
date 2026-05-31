<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

extract($displayData); ?>

<?php echo $form->renderField('og_title', 'metadata'); ?>
<?php echo $form->renderField('og_description', 'metadata'); ?>
<?php echo $form->renderField('og_image', 'metadata'); ?>
<?php echo $form->renderField('og_type', 'metadata'); ?>
<?php echo $form->renderField('customize_x', 'metadata'); ?>
<?php echo $form->renderField('x_title', 'metadata'); ?>
<?php echo $form->renderField('x_description', 'metadata'); ?>
<?php echo $form->renderField('x_image', 'metadata'); ?>
