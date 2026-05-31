<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Tags\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class Router extends BaseDatabaseModel
{
    public function getTagIdFromAlias(string $alias): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from('#__tags')->where('alias = ' . $db->q($alias));

        if (Multilanguage::isEnabled()) {
            $query->where($db->qn('language') . ' IN(' . $db->q('*') . ', ' . $db->q($this->getState('language')) . ')');
        }
        $db->setQuery($query);
        $id = (int)$db->loadResult();

        return $id;
    }

}
