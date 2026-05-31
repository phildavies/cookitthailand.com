<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2020 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted Access');

?>
<?php if (version_compare(JVERSION, '4', 'lt')): ?>
    <div class="alert alert-no-items">
        <?= Text::_( 'COM_JCHOPTIMIZE_NO_RECORDS' ); ?>:
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">Info</span>
        <?= Text::_('COM_JCHOPTIMIZE_NO_RECORDS'); ?>
    </div>
<?php endif; ?>