<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Table;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CrawlerTaskTable extends Table
{
    protected $_supportNullValue = true;

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__route66_crawler_tasks', 'id', $db, $dispatcher);
    }

    public function store($updateNulls = true)
    {
        $user  = Factory::getApplication()->getIdentity();
        $date  = new Date();

        if ($this->hasPrimaryKey()) {
            $this->modified = $date->toSql();
        } else {
            $this->created    = $date->toSql();
            $this->created_by = $user->id;
            $this->modified   = $date->toSql();
        }

        return parent::store($updateNulls);
    }
}
