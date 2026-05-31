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

class ContentAnalysisTable extends Table
{
    protected $_supportNullValue = true;

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__route66_content_analysis', 'id', $db, $dispatcher);
    }

    public function store($updateNulls = true)
    {
        if (\is_string($this->seo_keyphrase)) {
            $this->seo_keyphrase = trim($this->seo_keyphrase);
        }

        return parent::store($updateNulls);
    }
}
