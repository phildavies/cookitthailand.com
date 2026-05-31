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

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

class GSDTableItem extends Table
{
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) 
    {
    	$this->setColumnAlias('published', 'state');
    	$this->created = Factory::getDate()->toSql();

        parent::__construct('#__gsd', 'id', $db);
    }
}