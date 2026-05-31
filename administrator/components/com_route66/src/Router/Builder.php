<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Router;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Router\SiteRouter;

\defined('_JEXEC') or die;

final class Builder
{
    private $rules = [];
    private $route = '';
    private $menu;
    private $languages;
    private $defaultLanguage;
    private $cache = [];

    public function __construct(array $rules, SiteMenu $menu)
    {
        $this->rules           = $rules;
        $this->menu            = $menu;
        $this->languages       = LanguageHelper::getLanguages('lang_code');
        $defaultLanguage       = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        $this->defaultLanguage = isset($this->languages[$defaultLanguage]) ? $this->languages[$defaultLanguage] : current($this->languages);

        $this->attachRules();
    }

    private function attachRules()
    {
        $router = Factory::getContainer()->get(SiteRouter::class);
        $router->attachBuildRule([$this, 'preBuildExtension'], Router::PROCESS_BEFORE);
        $router->attachBuildRule([$this, 'postBuild'], Router::PROCESS_BEFORE);
    }


    public function preBuildExtension(&$router, &$uri)
    {
        // Reset route
        $this->route = '';

        // Get query
        $query = $uri->getQuery(true);

        // Quick check for the provided menu item
        if ($this->hasMenuItem($query)) {
            return;
        }

        // Second check - If provided menu item is empty or does not match search for a menu item
        if ($this->findMenuItem($query)) {
            return;
        }

        // Detect language
        if (Multilanguage::isEnabled()) {

            $language = $this->defaultLanguage->lang_code;

            if (isset($query['lang']) && $query['lang'] && isset($this->languages[$query['lang']])) {
                $language = $this->languages[$query['lang']]->lang_code;
            }
        }

        // Iterate over rules
        foreach ($this->rules as $rule) {

            // Filter by language
            if (Multilanguage::isEnabled() && $rule->getLanguage() != $language) {
                continue;
            }

            // Rule variables
            $variables = $rule->getVariables();

            // Option
            if (!isset($query['option']) || $query['option'] !== $variables['option']) {
                continue;
            }

            // Fallback for extensions that do not support view ...
            if (isset($query['ctrl']) && !isset($query['view'])) {
                $query['view'] = $query['ctrl'];
            }

            if (isset($variables['ctrl']) && !isset($variables['view'])) {
                $variables['view'] = $variables['ctrl'];
            }

            // View
            if (!isset($query['view']) || $query['view'] !== $variables['view']) {
                continue;
            }

            // Rule Key
            $key = $rule->getKey();

            if (!isset($query[$key]) || !$query[$key]) {
                continue;
            }

            // Unset the Itemid and keep active menu item
            $activeMenuItem = null;
            if (isset($query['Itemid'])) {
                if ($query['Itemid']) {
                    $activeMenuItem = $this->menu->getItem($query['Itemid']);
                }
                unset($query['Itemid']);
            }

            // Check for cache
            $cacheKey = $this->getCacheKey($query, $key);

            if (isset($this->cache[$cacheKey])) {

                $this->route = $this->cache[$cacheKey];

            } else {

                // The new route is the pattern with the tokens replaced with the corresponsing item data
                $this->route = str_replace($rule->getTokens(true), $rule->getItemData($query), $rule->getPattern());

                // Cache
                $this->cache[$cacheKey] = $this->route;
            }

            // Unset all route variables
            foreach ($variables as $variable => $value) {
                if (isset($query[$variable])) {
                    unset($query[$variable]);
                }
            }

            // Unset menu variables since they are not direct menu link. We already have all we need
            if ($activeMenuItem) {
                foreach ($activeMenuItem->query as $variable => $value) {
                    if (isset($query[$variable])) {
                        unset($query[$variable]);
                    }
                }
            }

            // Remove any empty variables
            $query = array_filter($query);

            // Update the query
            $uri->setQuery($query);
        }

    }

    public function postBuild(&$router, &$uri)
    {
        if ($this->route) {
            $uri->setPath($uri->getPath(). '/'. $this->route);
        }
    }

    public function hasMenuItem(array $query): bool
    {
        $Itemid = $query['Item'] ?? null;

        if (!$Itemid) {
            return false;
        }

        $component = $query['option'] ?? null;

        if (!$component) {
            return false;
        }

        $view = $query['view'] ?? null;

        if (!$view) {
            return false;
        }

        $menuItem = $this->menu->getItem($Itemid);

        if (!$menuItem) {
            return false;
        }

        $menuComponent =  $item->query['option'] ?? null;
        $menuView      = $item->query['view'] ?? null;

        return $component === $menuComponent && $view === $menuView;
    }

    public function findMenuItem($query)
    {
        if (!isset($query['option'])) {
            return false;
        }

        $component = ComponentHelper::getComponent($query['option']);

        if (!$component) {
            return false;
        }

        $conditions = ['component_id'];
        $values     = [$component->id];

        if (Multilanguage::isEnabled()) {

            $languages = ['*'];

            foreach ($this->languages as $language) {
                $languages[] = $language->lang_code;
            }

            $conditions[] = 'language';
            $values[]     = $languages;
        }

        $items = $this->menu->getItems($conditions, $values);

        $result = false;

        foreach ($items as $item) {

            $vars = $item->query;

            $total    = \count($vars);
            $matching = 0;

            foreach ($vars as $key => $value) {

                if (is_numeric($value)) {
                    $check = isset($query[$key]) && (int) $query[$key] === (int) $value;
                } else {
                    $check = isset($query[$key]) && $query[$key] == $value;
                }

                if ($check) {
                    $matching++;
                }
            }

            if ($matching == $total) {
                $result = true;

                break;
            }
        }

        return $result;
    }

    private function getCacheKey(array $query, string $key): string
    {
        $id = \is_array($query[$key]) ? implode($query[$key]) : (int) $query[$key];

        return $query['option'].'_'.$query['view'].'_'.$id ;
    }
}
