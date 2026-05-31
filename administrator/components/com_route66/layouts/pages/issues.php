<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

extract($displayData); ?>

<?php if (\count($issues)): ?>
<?php foreach ($issues as $issue): ?>
    <span class="badge text-bg-<?php echo $issue->type; ?>"><?php echo $issue->label; ?></span>
<?php endforeach; ?>
<?php else: ?>
    <span class="badge text-bg-success"><?php echo Text::_('COM_ROUTE66_NO_ISSUES'); ?></span>
<?php endif; ?>