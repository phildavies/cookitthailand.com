<?php

/**
 * @package         Google Structured Data
 * @version         6.2.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

// Register Convert Form namespace
JLoader::registerNamespace('GSD', JPATH_ADMINISTRATOR . '/components/com_gsd/GSD', false, false, 'psr4');

// Include Composer Autoload
$autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoload))
{
	require_once $autoload;

} elseif (Factory::getApplication()->isClient('administrator'))
{
	Factory::getApplication()->enqueueMessage('Tassos Framework Vendor Autoload Failed. File <b>' . $autoload . '</b> not found.', 'error');
}