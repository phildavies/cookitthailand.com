<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Tags\Router;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Router\Rule;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Tags\Site\Helper\RouteHelper;

class Tag extends Rule
{
    public const TOKENS = [
        'tagId'    => '(?P<tagId>\d+)',
        'tagAlias' => '(?P<tagAlias>[\w-]+)',
        'tagTitle' => '(?P<tagTitle>[\w-]+)',
    ];

    public const IDENTIFIERS = [
        'tagId',
        'tagAlias',
    ];

    public const VARIABLES =  [
        'option' => 'com_tags',
        'view'   => 'tag',
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

        // Prepare IDs
        if (isset($query['id']) && \is_array($query['id'])) {
            foreach ($query['id'] as &$id) {
                $id = (int) $id;
            }
        }

        // Iterate over the tokens
        foreach ($this->tokens as $token) {
            // ID
            if ($token == 'tagId') {
                $dbQuery->select($db->qn('id'));
            }
            // Alias
            elseif ($token == 'tagAlias') {
                if (\is_string($query['id']) && strpos($query['id'], ':')) {
                    $parts    = explode(':', $query['id']);
                    $values[] = $parts[1];
                }
                $dbQuery->select($db->qn('alias'));
            }
            // Title
            elseif ($token == 'tagTitle') {
                $dbQuery->select($db->qn('title'));
            }
        }

        // Check if we already have what we need
        if (\count($this->tokens) === \count($values)) {
            return $values;
        }

        // If not let's query the database
        if ($dbQuery->select) {
            $dbQuery->from($db->qn('#__tags'));
            $dbQuery->where($db->qn('id') . ' IN (' . implode(',', $query['id']). ')');
            $db->setQuery($dbQuery);
            $values = (array) $db->loadRow();
        }

        return $values;
    }

    public function getItemKey(array $results): int
    {
        // First check that ID is not already in the URL
        if (isset($results['tagId'])) {
            return (int) $results['tagId'];
        }

        // Check for alias
        if (isset($results['tagAlias'])) {
            $this->model->setState('language', $this->getLanguage());
            return (int) $this->model->getTagIdFromAlias($results['tagAlias']);
        }

        return 0;

    }

    public function getItemid(array $variables): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('id'))->select($db->qn('language'))->from($db->qn('#__tags'))->where($db->qn('id') . ' = ' . $db->q($variables['id']));
        $db->setQuery($query);
        $tag = $db->loadObject();

        $route  = RouteHelper::getComponentTagRoute($tag->id, $tag->language);
        $router = Factory::getContainer()->get(SiteRouter::class);
        $uri    = new Uri($route);
        $router->buildComponentPreprocess($router, $uri);
        $route = $uri->toString();

        parse_str($route, $result);
        $Itemid = isset($result['Itemid']) ? (int) $result['Itemid'] : 0;

        return $Itemid;
    }


}
