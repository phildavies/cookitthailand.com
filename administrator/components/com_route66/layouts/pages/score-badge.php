<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

extract($displayData); ?>

<?php if ($rating === 'good'): ?>
<span class="badge text-bg-success"><?php echo Text::_('COM_ROUTE66_GOOD') ;?></span>
<?php elseif ($rating === 'ok'): ?>
<span class="badge text-bg-warning"><?php echo Text::_('COM_ROUTE66_OK') ;?></span>
<?php elseif ($rating === 'bad'): ?>
<span class="badge text-bg-danger"><?php echo Text::_('COM_ROUTE66_NEEDS_IMPROVEMENT') ;?></span>
<?php else: ?>
<span class="badge text-bg-secondary"><?php echo Text::_('COM_ROUTE66_NA') ;?></span>
<?php endif; ?>
