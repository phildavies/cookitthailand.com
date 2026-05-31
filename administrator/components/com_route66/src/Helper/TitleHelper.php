<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class TitleHelper
{
    public static function addSiteName($title)
    {
        $application     = Factory::getApplication();
        $siteName        = $application->get('sitename');
        $siteNameInTitle = $application->get('sitename_pagetitles');

        if ($siteNameInTitle == 1) {
            $title = Text::sprintf('JPAGETITLE', $siteName, $title);
        } elseif ($siteNameInTitle == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $siteName);
        }

        return $title;
    }

    public static function removeSiteName($title, $lang = 'en-GB')
    {
        $application     = Factory::getApplication();
        $siteNameInTitle = $application->get('sitename_pagetitles');

        if (!$siteNameInTitle) {
            return $title;
        }

        $language = Factory::getLanguage();
        $current  = $language->getTag();

        $language->load('joomla', JPATH_SITE, $lang);

        $siteName = $application->get('sitename');
        $search   = $siteNameInTitle == 1 ? Text::sprintf('JPAGETITLE', $siteName, '') : Text::sprintf('JPAGETITLE', '', $siteName);
        $title    = str_replace($search, '', $title);

        // Restore current language
        $language->load('joomla', JPATH_BASE, $current, true);

        return $title;
    }
}
