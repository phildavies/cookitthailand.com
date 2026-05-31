<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Router;

use Joomla\CMS\Factory;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Router\SiteRouter;

\defined('_JEXEC') or die;

final class Parser
{
    private array $rules = [];
    private SiteMenu $menu;
    private array $vars       = [];
    private bool $isMenuItem  = false;

    public function __construct(array $rules, SiteMenu $menu)
    {
        $this->rules      = $rules;
        $this->menu       = $menu;

        $this->attachRules();
    }

    private function attachRules()
    {
        $router = Factory::getContainer()->get(SiteRouter::class);
        $router->attachParseRule([$this, 'preParseMenu'], Router::PROCESS_BEFORE);
        $router->attachParseRule([$this, 'preParseExtension'], Router::PROCESS_BEFORE);
        $router->attachParseRule([$this, 'postParse'], Router::PROCESS_AFTER);
    }

    public function preParseMenu(&$router, &$uri)
    {
        $path = $uri->getPath();

        if ($path === '') {
            $this->isMenuItem = true;

            return;
        }

        $items = $this->menu->getItems('route', $path);

        $this->isMenuItem = \count($items) > 0 ? true : false;
    }

    public function preParseExtension(&$router, &$uri)
    {
        if ($this->isMenuItem) {
            return;
        }

        $path = $uri->getPath();

        if (strpos($path, 'component/') === 0) {
            return;
        }

        foreach ($this->rules as $rule) {

            $this->vars = [];

            $results = $this->match($rule->getRegex(), $rule->getTokens(), $path);

            if (!\count($results)) {
                continue;
            }

            $variables = $rule->getVariables();
            $key       = $rule->getKey();

            foreach ($variables as $index => $value) {
                $this->vars[$index] = $index === $key ? $rule->getItemKey($results) : $value;
            }

            // Key is required
            if (!isset($this->vars[$key]) || !$this->vars[$key]) {
                $this->vars = [];
                continue;
            }

            // Ensure that key is numeric
            if (!is_numeric($this->vars[$key])) {
                $this->vars = [];
                continue;
            }

            // Verify what we found is correct
            $link     = trim(Route::_('index.php?' . http_build_query($this->vars)), '/');
            $computed = trim($uri->getPath(), '/');

            if (!str_contains($link, $computed)) {
                $this->vars = [];
                continue;
            }

            // Restore Itemid so the module assignments keep working
            $this->vars['Itemid'] = $rule->getItemid($this->vars);


            $uri->setPath('');

            break;
        }
    }

    public function postParse(&$router, &$uri)
    {
        if ($this->isMenuItem) {
            return;
        }

        if (!\count($this->vars)) {
            return;
        }

        $router->setVars($this->vars);

        $uri->setQuery(array_merge($uri->getQuery(true), $this->vars));

        if (isset($this->vars['Itemid'])) {
            $this->menu->setActive($this->vars['Itemid']);
        }
    }

    private function match(string $regex, array $tokens, string $path): array
    {
        $results = [];

        if (!$regex) {
            return $results;
        }

        if (!\count($tokens)) {
            return $results;
        }

        if (!$path) {
            return $results;
        }

        preg_match_all($regex, $path, $matches, PREG_SET_ORDER);

        if (\is_array($matches) && \count($matches)) {
            $results = array_intersect_key($matches[0], array_flip($tokens));
        }

        return $results;
    }
}
