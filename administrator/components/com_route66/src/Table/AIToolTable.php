<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Table;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AIToolTable extends Table implements VersionableTableInterface
{
    protected $_supportNullValue = true;

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        $this->typeAlias = 'com_route66.aitool';
        parent::__construct('#__route66_ai_tools', 'id', $db, $dispatcher);
        $this->setColumnAlias('published', 'state');
    }

    public function getTypeAlias()
    {
        return $this->typeAlias;
    }

    public function check()
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        if (!$this->id) {
            $this->created    = $date->toSql();
            $this->created_by = $user->id;
            $this->ordering   = $this->getNextOrder();
        } else {
            $this->modified    = $date->toSql();
            $this->modified_by = $user->id;
        }

        return true;
    }
}
