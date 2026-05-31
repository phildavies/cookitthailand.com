<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class MetadataTable extends Table
{
    protected $_supportNullValue = true;

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__route66_metadata', 'id', $db, $dispatcher);
    }

    public function store($updateNulls = true)
    {
        if (\is_string($this->title)) {
            $this->title = trim($this->title);
        }

        if (\is_string($this->description)) {
            $this->description = trim($this->description);
        }

        if (\is_string($this->robots)) {
            $this->robots = trim($this->robots);
        }

        if (\is_string($this->canonical)) {
            $this->canonical = trim($this->canonical);
        }

        return parent::store($updateNulls);
    }
}
