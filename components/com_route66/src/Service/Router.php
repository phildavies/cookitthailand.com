<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\Service;

use Joomla\CMS\Component\Router\RouterBase;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class Router extends RouterBase
{
    public function build(&$query)
    {
        $segments = [];

        if (isset($query['view'])) {
            $segments[] = $query['view'];
            unset($query['view']);
        }

        if (isset($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }

        if (isset($query['extension'])) {
            $segments[] = $query['extension'];
            unset($query['extension']);
        }

        return $segments;
    }


    public function parse(&$segments)
    {
        $vars = ['view' => $segments[0]];

        unset($segments[0]);

        if (isset($segments[1])) {
            $vars['id'] = $segments[1];
        }

        if (isset($segments[2])) {
            $vars['extension'] = $segments[2];
        }

        $segments = [];

        return $vars;
    }
}
