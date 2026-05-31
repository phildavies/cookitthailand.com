<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class HashHelper
{
    public static function generateHash($payload)
    {
        $token = Session::getFormToken();

        if (isset($payload[$token])) {
            unset($payload[$token]);
        }

        $application = Factory::getApplication();

        return hash('md5', json_encode($payload).$application->get('secret'));
    }

    public static function checkHash($hash, $payload)
    {
        $application = Factory::getApplication();

        if (!$application->get('secret')) {
            return false;
        }

        if (!$hash) {
            return false;
        }

        if (isset($payload['hash'])) {
            unset($payload['hash']);
        }

        $token = Session::getFormToken();

        if (isset($payload[$token])) {
            unset($payload[$token]);
        }

        return static::generateHash($payload) === $hash;
    }
}
