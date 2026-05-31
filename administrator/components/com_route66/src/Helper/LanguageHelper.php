<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\LanguageHelper as CoreLanguageHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class LanguageHelper
{
    public static function detectLanguage(string $browserLanguage): string
    {
        $lang = $browserLanguage;

        if (!str_contains($lang, '-')) {
            $languages = CoreLanguageHelper::getContentLanguages();
            foreach ($languages as $language) {
                if (str_starts_with($language->lang_code, $lang)) {
                    $lang = $language->lang_code;
                    break;
                }
            }
        }

        $language = CoreLanguageHelper::getMetadata($lang);

        if (!$language) {
            return ComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        }

        return $language['name'];
    }
}
