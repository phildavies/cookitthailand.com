<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Firecoders\Component\Route66\Administrator\Crawler\Observer;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Spatie\Crawler\Crawler;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PageHelper
{
    public static function fetch(string $url, $pageId = null): void
    {
        $robotsTxt           = CrawlerHelper::getRobotsTxt();
        $options             = CrawlerHelper::getOptions();
        $observer            = new Observer($robotsTxt);
        $observer->setPageId($pageId);

        $crawler = Crawler::create($options);
        $crawler->setTotalCrawlLimit(1);
        $crawler->ignoreRobots();
        $crawler->acceptNofollowLinks();
        $crawler->setParseableMimeTypes(['text/html']);
        $crawler->setCrawlObserver($observer);
        $crawler->startCrawling($url);
    }

    public static function discover(): void
    {
        if (!Route66Helper::isPro()) {
            return;
        }

        $document = Factory::getDocument();

        $canonical = MetadataHelper::getCanonical($document);
        $uri       = $canonical && Uri::isInternal($canonical) ? $canonical : Uri::getInstance()->toString();

        $payload = [
            'title'       => (string) $document->getTitle(),
            'description' => (string) $document->getDescription(),
            'uri'         => $uri,
        ];

        $payload['hash'] = HashHelper::generateHash($payload);

        $document->addScriptOptions('route66.discover', $payload);

        $wa = $document->getWebAssetManager();
        $wa->registerAndUseScript('route66.page.discover', 'route66/page/discover.js', [], ['defer' => true]);
    }

    public static function getResourceId(): string
    {
        $application = Factory::getApplication();
        $option      = $application->input->getCmd('option');
        $view        = $application->input->getCmd('view');

        if (!$option || !$view) {
            return '';
        }

        $resourceKey = PageHelper::getResourceKey($option, $view);

        if (!$resourceKey) {
            return '';
        }

        $key = $application->input->get($resourceKey);

        if (!is_numeric($key)) {
            return '';
        }

        if (!$key) {
            return '';
        }

        return $option.'.'.$view.'.'.$key;
    }

    public static function getResourceKey($option, $view): string
    {
        if (!$option || !$view) {
            return '';
        }

        PluginHelper::importPlugin('route66');
        $key = Factory::getApplication()->triggerEvent('onRoute66AnalyzerResourceKey', [$option, $view]);

        if (!\is_string($key)) {
            return '';
        }

        return $key;
    }

    public static function hash(string $input): string
    {
        return hash('sha1', mb_strtolower(trim($input), 'UTF-8'));
    }

    public static function extract()
    {
        $application = Factory::getApplication();

        if (!$application->isClient('site')) {
            return;
        }

        $hash = HashHelper::generateHash(['uri' => Uri::current()]);

        if ($application->input->getInt($hash)) {
            echo Factory::getDocument()->getBuffer('component');
            $application->close();
        }
    }
}
