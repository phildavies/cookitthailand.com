<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Content\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

class Sitemap extends ListModel
{
    public function getListQuery()
    {
        $user  = Factory::getApplication()->getIdentity();
        $date  = Factory::getDate();

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select($db->qn('article.id'));
        $query->select($db->qn('article.title'));
        $query->select($db->qn('article.alias'));
        $query->select($db->qn('article.catid'));
        $query->select($db->qn('article.language'));
        $query->select($db->qn('article.images'));
        $query->select($db->qn('article.publish_up'));
        $query->select($db->qn('article.created'));
        $query->select($db->qn('article.modified'));
        $query->from($db->qn('#__content', 'article'));
        $query->where($db->qn('article.state') . ' IN(1,2)');
        $query->where($db->qn('article.access') . ' IN(' . implode(',', $user->getAuthorisedViewLevels()) . ')');
        $query->where('article.publish_up <= ' . $db->q($date->toSql()))->where('(article.publish_down IS NULL OR article.publish_down = ' . $db->q($db->getNullDate()) . ' OR article.publish_down >= ' . $db->q($date->toSql()) . ')');

        $sources = $this->getState('sources');
        if ($sources && $sources instanceof Registry) {
            $categories = $sources->get('contentCategories');
            if ($sources->get('content') == 2 && \is_array($categories) && \count($categories)) {
                $query->where($db->qn('article.catid') . ' IN(' . implode(',', $categories) . ')');
            }
        }

        $query->select($db->qn('category.title', 'categoryTitle'));
        $query->leftJoin($db->qn('#__categories', 'category') . ' ON ' . $db->qn('article.catid') . ' = ' . $db->qn('category.id'));
        $query->where($db->qn('category.published') . ' = 1')->where($db->qn('category.access') . ' IN(' . implode(',', $user->getAuthorisedViewLevels()) . ')');

        $settings = $this->getState('settings');
        if ($settings && $settings instanceof Registry) {
            if ($settings->get('type') == 'news') {
                $start = Factory::getDate(strtotime('-2 day', $date->toUnix()));
                $query->where($db->qn('article.publish_up') . ' >= ' . $db->q($start->toSql()));
                $this->setState('list.start', 0);
                $this->setState('list.limit', 0);
            }
        }

        $query->order($db->qn('article.id'));

        return $query;
    }

    public function getItems()
    {
        $application = Factory::getApplication();
        $timezone    = new \DateTimeZone($application->get('offset'));
        $settings    = $this->getState('settings');

        $items = parent::getItems();

        foreach ($items as $item) {

            $item->url = Route::_(RouteHelper::getArticleRoute($item->id . ':' . $item->alias, $item->catid, $item->language), true, Route::TLS_IGNORE, true);

            $images = $item->images;

            $item->videos = [];
            $item->images = [];

            if ($settings->get('type') != 'news' && $settings->get('images') && $images) {

                $images = json_decode($images);

                if (isset($images->image_fulltext) && $images->image_fulltext) {

                    $images->image_fulltext = MediaHelper::getCleanMediaFieldValue($images->image_fulltext);

                    $image = (object)['caption' => $images->image_fulltext_caption, 'url' => ''];

                    if (strpos($images->image_fulltext, 'http://') === 0 || strpos($images->image_fulltext, 'https://') === 0 || strpos($images->image_fulltext, '//') === 0) {
                        $image->url = $images->image_fulltext;
                    } else {
                        $image->url = Uri::root(false) . $images->image_fulltext;
                    }

                    $item->images[] = $image;
                }

                if (isset($images->image_intro) && $images->image_intro) {

                    $images->image_intro = MediaHelper::getCleanMediaFieldValue($images->image_intro);

                    $image = (object)['caption' => $images->image_intro_caption, 'url' => ''];

                    if (strpos($images->image_intro, 'http://') === 0 || strpos($images->image_intro, 'https://') === 0 || strpos($images->image_intro, '//') === 0) {
                        $image->url = $images->image_intro;
                    } else {
                        $image->url = Uri::root(false) . $images->image_intro;
                    }

                    $item->images[] = $image;
                }
            }

            $publicationDate = Factory::getDate($item->publish_up);
            $publicationDate->setTimeZone($timezone);
            $item->publicationDate = $publicationDate->toISO8601(true);

            $modified = (int) $item->modified > 0 ? $item->modified : $item->created;

            $modifiedDate = Factory::getDate($modified);
            $modifiedDate->setTimeZone($timezone);
            $item->modifiedDate = $modifiedDate->toISO8601(true);
        }

        return $items;
    }
}
