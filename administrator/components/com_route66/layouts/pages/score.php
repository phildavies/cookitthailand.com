<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);
?>

<?php if ($rating === 'good'): ?>
<span class="text-success fs-4 hasTooltip" title="<?php echo HTMLHelper::tooltipText('COM_ROUTE66_GOOD'); ?>"><i class="fa-solid fa-circle-check"></i></span>
<?php elseif ($rating === 'ok'): ?>
<span class="text-warning fs-4 hasTooltip" title="<?php echo HTMLHelper::tooltipText('COM_ROUTE66_OK'); ?>"><i class="fa-solid fa-circle-exclamation"></i></span>
<?php elseif ($rating === 'bad'): ?>
<span class="text-danger fs-4 hasTooltip" title="<?php echo HTMLHelper::tooltipText('COM_ROUTE66_NEEDS_IMPROVEMENT'); ?>"><i class="fa-solid fa-circle-xmark"></i></span>
<?php else: ?>
<span class="text-secondary fs-4 hasTooltip" title="<?php echo HTMLHelper::tooltipText('COM_ROUTE66_NA'); ?>"> - </span>
<?php endif; ?>
