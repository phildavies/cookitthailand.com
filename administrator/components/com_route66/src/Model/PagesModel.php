<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Firecoders\Component\Route66\Administrator\Helper\AnalyzerHelper;
use Firecoders\Component\Route66\Administrator\Issues\ExcessiveDOMSizeIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoCompressionIssue;
use Firecoders\Component\Route66\Administrator\Issues\PageSizeIssue;
use Firecoders\Component\Route66\Administrator\Issues\PageTimeIssue;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class PagesModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'page.id',
                'title', 'page.title',
                'size', 'page.size',
                'time', 'page.time',
                'crawled', 'page.crawled',
                'issues', 'response_type',
                'seo_rating', 'readability_rating',
            ];
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = 'page.id', $direction = 'desc')
    {
        $application = Factory::getApplication();

        // No state for duplicates layout
        if ($application->input->get('layout') === 'duplicates') {
            $this->context .= '.' . uniqid();
        }

        $this->setState('params', ComponentHelper::getParams('com_route66'));

        $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->getUserStateFromRequest($this->context . '.filter.id', 'filter_id');
        $this->getUserStateFromRequest($this->context . '.filter.exclude_id', 'filter_exclude_id');
        $this->getUserStateFromRequest($this->context . '.filter.title_hash', 'filter_title_hash');
        $this->getUserStateFromRequest($this->context . '.filter.description_hash', 'filter_description_hash');
        $this->getUserStateFromRequest($this->context . '.filter.link_hash', 'filter_link_hash');
        $this->getUserStateFromRequest($this->context . '.filter.rescource_id', 'filter_rescource_id');
        $this->getUserStateFromRequest($this->context . '.filter.seo_rating', 'filter_seo_rating');
        $this->getUserStateFromRequest($this->context . '.filter.readability_rating', 'filter_readability_rating');
        $this->getUserStateFromRequest($this->context . '.filter.issues', 'filter_issues');
        $this->getUserStateFromRequest($this->context . '.filter.response_type', 'filter_response_type');

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . serialize($this->getState('filter.id'));
        $id .= ':' . serialize($this->getState('filter.exclude_id'));
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.title_hash');
        $id .= ':' . $this->getState('filter.description_hash');
        $id .= ':' . $this->getState('filter.rescource_id');
        $id .= ':' . serialize($this->getState('filter.issues'));
        $id .= ':' . serialize($this->getState('filter.seo_rating'));
        $id .= ':' . serialize($this->getState('filter.readability_rating'));
        $id .= ':' . serialize($this->getState('filter.response_type'));

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($this->getState('list.select', 'page.*'));
        $query->from('#__route66_pages AS page');

        $id = $this->getState('filter.id');
        if ($id && is_numeric($id)) {
            $query->where($db->qn('page.id') . ' = '.$db->q($id));
        } elseif ($id && \is_array($id)) {
            ArrayHelper::toInteger($id);
            $query->where($db->qn('page.id') . ' IN ('.implode(',', $id).')');
        }

        $excludeId = $this->getState('filter.exclude_id');
        if ($excludeId && is_numeric($excludeId)) {
            $query->where($db->qn('page.id') . ' != '.$db->q($excludeId));
        } elseif ($excludeId && \is_array($excludeId)) {
            ArrayHelper::toInteger($excludeId);
            $query->where($db->qn('page.id') . ' NOT IN ('.implode(',', $excludeId).')');
        }

        $search = $this->getState('filter.search');

        if ($search) {
            if (is_numeric($search)) {
                $query->where($db->qn('page.id') . ' = '.$db->q($search));
            } else {
                $search = '%' . trim($search) . '%';
                $query->where($db->qn('page.title') . ' LIKE '.$db->q($search));
            }
        }

        if ($titleHash = $this->getState('filter.title_hash')) {
            $query->where($db->qn('page.title_hash') . ' = '.$db->q($titleHash));
        }

        if ($descriptionHash = $this->getState('filter.description_hash')) {
            $query->where($db->qn('page.description_hash') . ' = '.$db->q($descriptionHash));
        }

        if ($linkHash = $this->getState('filter.link_hash')) {
            $query->where($db->qn('page.link_hash') . ' = '.$db->q($linkHash));
        }

        if ($resourceId = $this->getState('filter.resource_id')) {
            $query->where($db->qn('page.resource_id') . ' = '.$db->q($resourceId));
        }

        if ($responseType = $this->getState('filter.response_type')) {

            switch ($responseType) {
                case 'normal':
                    $query->where('page.http_status = 200');
                    break;
                case 'redirect':
                    $query->where('page.http_status BETWEEN 301 AND 399');
                    break;
                case 'missing':
                    $query->where('page.http_status = 404');
                    break;
                case 'error':
                    $query->where('page.http_status >= 500');
                    break;
            }
        }

        if ($issues = $this->getState('filter.issues')) {

            $conditions = [];

            if (\in_array('no_title', $issues)) {
                $conditions[] = $db->qn('page.title_length') . ' = 0';
            }

            if (\in_array('no_description', $issues)) {
                $conditions[] = $db->qn('page.description_length') . ' = 0';
            }

            if (\in_array('duplicate_title', $issues)) {
                $conditions[] = $db->qn('page.duplicate_title') . ' =  1';
            }

            if (\in_array('duplicate_description', $issues)) {
                $conditions[] = $db->qn('page.duplicate_description') . ' =  1';
            }

            if (\in_array('duplicate_resource', $issues)) {
                $conditions[] = $db->qn('page.duplicate_resource') . ' =  1';
            }

            if (\in_array('no_index', $issues)) {
                $conditions[] = $db->qn('page.no_index') . ' = 1';
            }

            if (\in_array('no_follow', $issues)) {
                $conditions[] = $db->qn('page.no_follow') . ' = 1';
            }

            if (\in_array('robots_txt_blocked', $issues)) {
                $conditions[] = $db->qn('page.robots_txt_blocked') . ' = 1';
            }

            if (\in_array('slow_page', $issues)) {
                $conditions[] = $db->qn('page.time') . ' > '.PageTimeIssue::MAX_LOAD_TIME_MS;
            }

            if (\in_array('large_page', $issues)) {
                $conditions[] = $db->qn('page.size') . ' > '.(1024 * PageSizeIssue::MAX_RECOMMENDED_SIZE_KB);
            }

            if (\in_array('uncompressed', $issues)) {
                $conditions[] = $db->qn('page.content_encoding') . ' NOT IN ('.implode(',', $db->q(NoCompressionIssue::COMPRESSION_ENCODINGS)).')';
            }

            if (\in_array('dom_size', $issues)) {
                $conditions[] = $db->qn('page.dom_nodes') . ' > '.ExcessiveDOMSizeIssue::MAX_DOM_NODES;
            }

            if (\count($conditions) > 0) {
                $query->where('('. implode(' OR ', $conditions).')');
            }
        }

        $query->select($db->qn('analysis.seo_keyphrase'));
        $query->select($db->qn('analysis.seo_score'));
        $query->select($db->qn('analysis.readability_score'));

        $unionQuery = $this->getUnionQuery();

        if ($this->getState('filter.seo_rating') || $this->getState('filter.readability_rating')) {
            $query->innerJoin('('. $unionQuery .') AS analysis ON ' . $db->qn('page.id') . ' = ' . $db->qn('analysis.page_id'));
        } else {
            $query->leftJoin('('. $unionQuery .') AS analysis ON ' . $db->qn('page.id') . ' = ' . $db->qn('analysis.page_id'));
        }

        $query->order($this->getState('list.ordering', 'page.id') . ' ' . $this->getState('list.direction', 'DESC'));

        return $query;
    }

    protected function getUnionQuery()
    {
        $db = $this->getDatabase();

        $queries = [];

        $queries[] = $db->getQuery(true)->select('ca.page_id, ca.seo_keyphrase, ca.seo_score, ca.readability_score')->from($db->qn('#__route66_content_analysis', 'ca'))->where('ca.page_id IS NOT NULL');
        $queries[] = $db->getQuery(true)->select('p.id AS page_id, ca.seo_keyphrase, ca.seo_score, ca.readability_score')->from($db->qn('#__route66_content_analysis', 'ca'))->innerJoin($db->qn('#__route66_pages', 'p') . ' ON ca.resource_id = p.resource_id')->where('ca.page_id IS NULL')->where('ca.resource_id IS NOT NULL');
        $queries[] = $db->getQuery(true)->select('p.id AS page_id, ca.seo_keyphrase, ca.seo_score, ca.readability_score')->from($db->qn('#__route66_content_analysis', 'ca'))->innerJoin($db->qn('#__route66_pages', 'p') . ' ON ca.link_hash = p.link_hash')->where('ca.page_id IS NULL')->where('ca.link_hash IS NOT NULL');

        // Early filtering for better performance
        foreach ($queries as &$query) {

            if ($seoRating = $this->getState('filter.seo_rating')) {

                $range = AnalyzerHelper::ratingToScoreRange($seoRating);

                if (\count($range)) {
                    [$minimum, $maximum] = $range;
                    $query->where($db->qn('ca.seo_score') . ' > '.$minimum);
                    $query->where($db->qn('ca.seo_score') . ' <= '.$maximum);
                }
            }

            if ($readabilityRating = $this->getState('filter.readability_rating')) {

                $range = AnalyzerHelper::ratingToScoreRange($readabilityRating);

                if (\count($range)) {
                    [$minimum, $maximum] = $range;
                    $query->where($db->qn('ca.readability_score') . ' > '.$minimum);
                    $query->where($db->qn('ca.readability_score') . ' <= '.$maximum);
                }
            }

        }

        [$query1, $query2, $query3] = $queries;

        $query1->unionAll($query2);
        $query1->unionAll($query3);

        return $query1;
    }

    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item) {
            $item->url      = Route::link('site', $item->link, true, Route::TLS_IGNORE, true);
            $item->editLink = Route::link('administrator', 'index.php?option=com_route66&task=page.edit&id=' . $item->id);
        }

        return $items;
    }

    public function purge()
    {
        $db = $this->getDatabase();

        try {
            $db->truncateTable('#__route66_pages');

            $query = $db->getQuery(true);
            $query->delete($db->qn('#__route66_metadata'));
            $query->where($db->qn('resource_id').' IS NULL');
            $query->where($db->qn('link_hash').' IS NULL');
            $db->setQuery($query);
            $db->execute();

            $query = $db->getQuery(true);
            $query->delete($db->qn('#__route66_content_analysis'));
            $query->where($db->qn('resource_id').' IS NULL');
            $query->where($db->qn('link_hash').' IS NULL');
            $db->setQuery($query);
            $db->execute();

        } catch (\Exception) {
            return false;
        }

        return true;
    }

}
