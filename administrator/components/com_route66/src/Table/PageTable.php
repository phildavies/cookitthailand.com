<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Table;

use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PageTable extends Table
{
    protected $previousTitleHash       = '';
    protected $previousDescriptionHash = '';
    protected $_supportNullValue       = true;

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__route66_pages', 'id', $db, $dispatcher);
    }

    public function load($keys = null, $reset = true)
    {
        $result = parent::load($keys, $reset);

        if ($this->title_hash) {
            $this->previousTitleHash = $this->title_hash;
        }

        if ($this->description_hash) {
            $this->previousDescriptionHash = $this->description_hash;
        }

        return $result;
    }

    public function store($updateNulls = true)
    {
        if (\is_string($this->title)) {
            $this->title        = trim($this->title);
            $this->title_length = \strlen($this->title);
            $this->title_hash   = $this->title ? PageHelper::hash($this->title) : null;
        }

        if (\is_string($this->description)) {
            $this->description        = trim($this->description);
            $this->description_length = \strlen($this->description);
            $this->description_hash   = $this->description ? PageHelper::hash($this->description) : null;
        }

        if (\is_string($this->link)) {
            $this->link      = trim($this->link);
            $this->link_hash = PageHelper::hash($this->link);
        }

        if (\is_string($this->canonical)) {
            $this->canonical      = trim($this->canonical);
            $this->canonical_hash = PageHelper::hash($this->canonical);
        }

        if (\is_string($this->robots)) {
            $this->no_index  = str_contains($this->robots, 'noindex') ? 1 : 0;
            $this->no_follow = str_contains($this->robots, 'nofollow') ? 1 : 0;
        }

        unset($this->resource_id);

        try {

            $this->_db->transactionStart();

            $result = parent::store($updateNulls);

            if ($this->previousTitleHash && $this->previousTitleHash !== $this->title_hash) {
                $this->updateDuplicates('title_hash', $this->previousTitleHash, 'duplicate_title');
            }

            if ($this->title_hash) {
                $this->updateDuplicates('title_hash', $this->title_hash, 'duplicate_title');
            }

            if ($this->previousDescriptionHash && $this->previousDescriptionHash !== $this->description_hash) {
                $this->updateDuplicates('description_hash', $this->previousDescriptionHash, 'duplicate_description');
            }

            if ($this->description_hash) {
                $this->updateDuplicates('description_hash', $this->description_hash, 'duplicate_description');
            }

            $resourceId = $this->component . '.' . $this->view . '.' . $this->key;
            if ($resourceId) {
                $this->updateDuplicates('resource_id', $resourceId, 'duplicate_resource');
            }

            $this->_db->transactionCommit();

            return $result;

        } catch (Exception $e) {

            $this->_db->transactionRollback();
            return false;
        }
    }

    protected function updateDuplicates(string $conditionField, string $conditionValue, string $flagField)
    {
        $query = $this->_db->getQuery(true)->select('COUNT(*)')->from($this->_db->qn('#__route66_pages'))->where($this->_db->qn($conditionField) . ' = ' . $this->_db->q($conditionValue));
        $this->_db->setQuery($query);
        $count = (int) $this->_db->loadResult();

        $query = $this->_db->getQuery(true)->update($this->_db->qn('#__route66_pages'))->set($this->_db->qn($flagField) . ' = ' . ($count > 1 ? 1 : 0))->where($this->_db->qn($conditionField) . ' = ' . $this->_db->q($conditionValue));
        $this->_db->setQuery($query);
        $this->_db->execute();
    }
}
