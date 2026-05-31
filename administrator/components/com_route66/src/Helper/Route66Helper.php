<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Factory;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class Route66Helper
{
    public static function copyrights()
    {
        $extension = ExtensionHelper::getExtensionRecord('pkg_route66', 'package');
        $manifest  = json_decode($extension->manifest_cache);

        $date = Factory::getDate();
        $html = '<div class="text-center mt-4"><a target="_blank" href="https://www.firecoders.com/joomla-extensions/route66">' . $manifest->name . ' v' . $manifest->version . '</a> | Copyright &copy; 2016 - ' . $date->format('Y') . ' <a target="_blank" href="https://www.firecoders.com">Firecoders</a></div>';
        $html .= '<div class="text-center">If you like Route66, please post a review at the <a href="https://extensions.joomla.org/extension/route66/" target="_blank">Joomla Extensions Directory</a>.</div>';

        return $html;
    }

    public static function version()
    {
        $extension = ExtensionHelper::getExtensionRecord('pkg_route66', 'package');
        $manifest  = json_decode($extension->manifest_cache);

        return $manifest->version;
    }

    public static function options($form): void
    {
        $patternsField = $form->getField('patterns');
        $patternsForm  = $patternsField->getSubform();
        Factory::getApplication()->triggerEvent('onRoute66RouterForm', [&$patternsForm]);

        if (self::isPro()) {
            return;
        }

        $form->removeField('save_history');
        $form->removeField('history_limit');
        $form->removeField('ai_service');
        $form->removeField('openai_model');
        $form->removeField('openai_api_key');
        $form->removeField('anthropic_model');
        $form->removeField('anthropic_api_key');
        $form->setFieldAttribute('ai_note', 'description', 'COM_ROUTE66_AI_TOOLS_PRO_FEATURE');
        $form->setFieldAttribute('urls_note', 'description', 'COM_ROUTE66_URLS_PRO_NOTE');
    }

    public static function setVersion()
    {
        $extension = ExtensionHelper::getExtensionRecord('pkg_route66', 'package');
        $manifest  = json_decode($extension->manifest_cache);

        \define('ROUTE66_VERSION', $manifest->name === 'Route 66 PRO' ? 'pro' : 'free');
    }

    public static function isPro(): bool
    {
        if (!\defined('ROUTE66_VERSION')) {
            return false;
        }

        return ROUTE66_VERSION === 'pro' ;
    }

    public static function setFeatures()
    {
        if (self::isPro()) {
            return;
        }

        $params = ComponentHelper::getParams('com_route66');
        $params->set('save_history', 0);
        $params->set('total_crawl_limit', 30);
        $params->set('ai_service', '');
    }
}
