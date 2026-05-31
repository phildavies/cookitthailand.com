<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Content\Router;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Router\Rule;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

class Category extends Rule
{
    public const TOKENS = [
        'categoryId'    => '(?P<categoryId>\d+)',
        'categoryAlias' => '(?P<categoryAlias>[\w-]+)',
        'categoryPath'  => '(?P<categoryPath>[\w\/-]+)',
    ];

    public const IDENTIFIERS = [
        'categoryId',
        'categoryAlias',
        'categoryPath',
    ];

    public const VARIABLES =  [
        'option' => 'com_content',
        'view'   => 'category',
        'id'     => '',
    ];

    public const KEY = 'id';

    public function getItemData(array $query): array
    {
        // Get database
        $db = Factory::getDbo();

        // Get query
        $dbQuery = $db->getQuery(true);

        // Initialize values
        $values = [];

        // Iterate over the tokens
        foreach ($this->tokens as $token) {
            // ID
            if ($token == 'categoryId') {
                $values[] = (int) $query['id'];
                $dbQuery->select($db->qn('id'));
            }
            // Alias
            elseif ($token == 'categoryAlias') {
                if (strpos($query['id'], ':')) {
                    $parts    = explode(':', $query['id']);
                    $values[] = $parts[1];
                }
                $dbQuery->select($db->qn('alias'));
            }
            // Path
            elseif ($token == 'categoryPath') {
                $dbQuery->select($db->qn('path'));
            }
        }

        // Check if we already have what we need
        if (\count($this->tokens) === \count($values)) {
            return $values;
        }

        // If not let's query the database
        if ($dbQuery->select) {
            $dbQuery->from($db->qn('#__categories'));
            $dbQuery->where($db->qn('id') . ' = ' . (int) $query['id']);
            $db->setQuery($dbQuery);
            $values = (array) $db->loadRow();
        }

        return $values;
    }

    public function getItemKey(array $results): int
    {
        // First check that ID is not already in the URL
        if (isset($results['categoryId'])) {
            return (int) $results['categoryId'];
        }

        // Check for alias
        if (isset($results['categoryAlias'])) {
            $this->model->setState('language', $this->getLanguage());
            return (int) $this->model->getCategoryIdFromAlias($results['categoryAlias']);
        }

        // Check for path
        if (isset($results['categoryPath'])) {
            $this->model->setState('language', $this->getLanguage());
            return (int) $this->model->getCategoryIdFromPath($results['categoryPath']);
        }

        return 0;

    }

    public function getItemid(array $variables): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('id'))->select($db->qn('language'))->from($db->qn('#__categories'))->where($db->qn('id') . ' = ' . $db->q($variables['id']));
        $db->setQuery($query);
        $category = $db->loadObject();

        $route  = RouteHelper::getCategoryRoute($category->id, $category->language);
        $router = Factory::getContainer()->get(SiteRouter::class);
        $uri    = new Uri($route);
        $router->buildComponentPreprocess($router, $uri);
        $route = $uri->toString();

        parse_str($route, $result);
        $Itemid = isset($result['Itemid']) ? (int)$result['Itemid'] : 0;

        return $Itemid;
    }
}
