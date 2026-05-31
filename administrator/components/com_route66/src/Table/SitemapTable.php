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


class SitemapTable extends Table
{
    protected $_jsonEncode       = ['sources', 'settings'];
    protected $_supportNullValue = true;

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__route66_sitemaps', 'id', $db, $dispatcher);
        $this->setColumnAlias('published', 'state');
    }
}
