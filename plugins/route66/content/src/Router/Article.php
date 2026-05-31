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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

class Article extends Rule
{
    public const TOKENS = [
        'articleId'     => '(?P<articleId>\d+)',
        'articleAlias'  => '(?P<articleAlias>[\w-]+)',
        'articleYear'   => '(?P<articleYear>\d{4})',
        'articleMonth'  => '(?P<articleMonth>\d{2})',
        'articleDay'    => '(?P<articleDay>\d{2})',
        'articleDate'   => '(?P<articleDate>\d{4}-\d{2}-\d{2})',
        'articleAuthor' => '(?P<articleAuthor>[\w-]+)',
        'categoryAlias' => '(?P<categoryAlias>[\w-]+)',
        'categoryPath'  => '(?P<categoryPath>[\w\/-]+)',
    ];

    public const IDENTIFIERS = [
        'articleId',
        'articleAlias',
    ];

    public const VARIABLES =  [
        'option' => 'com_content',
        'view'   => 'article',
        'id'     => '',
        'catid'  => '',
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
            if ($token == 'articleId') {
                $values[] = (int) $query['id'];
                $dbQuery->select($db->qn('article.id'));
            }
            // Alias
            elseif ($token == 'articleAlias') {
                if (strpos($query['id'], ':')) {
                    $parts    = explode(':', $query['id']);
                    $values[] = $parts[1];
                }
                $dbQuery->select($db->qn('article.alias'));
            }
            // Category alias
            elseif ($token == 'categoryAlias') {
                $dbQuery->select($db->qn('category.alias'));
            }
            // Category path
            elseif ($token == 'categoryPath') {
                $dbQuery->select($db->qn('category.path'));
            }
            // Article year
            elseif ($token == 'articleYear') {
                $dbQuery->select('YEAR(' . $db->qn('article.created') . ')');
            }
            // Article month
            elseif ($token == 'articleMonth') {
                $dbQuery->select('DATE_FORMAT(' . $db->qn('article.created') . ', "%m")');
            }
            // Article day
            elseif ($token == 'articleDay') {
                $dbQuery->select('DATE_FORMAT(' . $db->qn('article.created') . ', "%d")');
            }
            // Article date
            elseif ($token == 'articleDate') {
                $dbQuery->select('DATE(' . $db->qn('article.created') . ')');
            }
            // Article author
            elseif ($token == 'articleAuthor') {
                $dbQuery->select('CASE WHEN ' . $db->qn('article.created_by_alias') . ' = ' . $db->q('') . ' THEN ' . $db->qn('article.created_by') . ' ELSE ' . $db->qn('article.created_by_alias') . ' END ');
            }
        }

        // Check if we already have what we need
        if (\count($this->tokens) === \count($values)) {
            return $values;
        }

        // If not let's query the database
        if ($dbQuery->select) {
            $dbQuery->from($db->qn('#__content', 'article'));
            $dbQuery->innerJoin($db->qn('#__categories', 'category') . ' ON ' . $db->qn('article.catid') . ' = ' . $db->qn('category.id'));
            $dbQuery->where($db->qn('article.id') . ' = ' . (int) $query['id']);
            $db->setQuery($dbQuery);
            $values = (array) $db->loadRow();
        }

        // Some values need processing
        $author = array_search('articleAuthor', $this->tokens);

        if ($author !== false && $values) {
            if (is_numeric($values[$author])) {
                $values[$author] = Factory::getUser($values[$author])->name;
            }
            $values[$author] = OutputFilter::stringURLUnicodeSlug($values[$author]);
        }

        return $values;
    }

    public function getItemKey(array $results): int
    {
        // First check that ID is not already in the URL
        if (isset($results['articleId'])) {
            return (int) $results['articleId'];
        }

        // Check for alias
        if (isset($results['articleAlias'])) {
            $this->model->setState('language', $this->getLanguage());
            return (int) $this->model->getArticleIdFromAlias($results['articleAlias']);
        }

        return 0;
    }

    public function getItemid(array $variables): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('id'))->select($db->qn('catid'))->select($db->qn('language'))->from($db->qn('#__content'))->where($db->qn('id') . ' = ' . $db->q($variables['id']));
        $db->setQuery($query);
        $article = $db->loadObject();

        $route  = RouteHelper::getArticleRoute($article->id, $article->catid, $article->language);
        $router = Factory::getContainer()->get(SiteRouter::class);
        $uri    = new Uri($route);
        $router->buildComponentPreprocess($router, $uri);
        $route = $uri->toString();

        parse_str($route, $result);
        $Itemid = isset($result['Itemid']) ? (int) $result['Itemid'] : 0;

        return $Itemid;
    }
}
