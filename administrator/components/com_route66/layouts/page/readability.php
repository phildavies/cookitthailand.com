<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

extract($displayData); ?>

<?php echo $form->renderField('readability_score', 'analysis'); ?>

<div id="route66-analyzer-readability-results"></div>
