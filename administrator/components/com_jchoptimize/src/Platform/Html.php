<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2020 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Platform;

use JchOptimize\Core\Admin\AbstractHtml;
use JchOptimize\Core\Exception;
use JchOptimize\Core\Uri\Uri;
use JchOptimize\Core\Uri\Utils;
use JchOptimize\GetApplicationTrait;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\SiteRouter;

use function defined;

defined('_JEXEC') or die('Restricted access');

class Html extends AbstractHtml
{
    use GetApplicationTrait;

    /**
     * Returns HTML of the front page
     *
     * @return string
     * @throws \Exception
     */
    public function getHomePageHtml(): string
    {
        try {
            JCH_DEBUG ? Profiler::mark('beforeGetHtml') : null;

            $response = $this->getHtml($this->getSiteUrl());

            JCH_DEBUG ? Profiler::mark('afterGetHtml') : null;

            return $response;
        } catch (Exception\ExceptionInterface $e) {
            $this->logger->error($this->getSiteUrl() . ': ' . $e->getMessage());

            JCH_DEBUG ? Profiler::mark('afterGetHtml') : null;

            throw new Exception\RuntimeException('Try reloading the front page to populate the Exclude options');
        }
    }

    /**
     * @param string $sUrl
     *
     * @return string
     * @psalm-suppress UndefinedInterfaceMethod
     */
    protected function getHtml(string $sUrl): string
    {
        $uri = Utils::uriFor($sUrl);
        $unOptimizedUri = Uri::withQueryValues($uri, ['jchnooptimize' => '1']);

        try {
            $response = $this->http->get($unOptimizedUri);
        } catch (\Exception $e) {
            throw new Exception\RuntimeException(
                'Exception fetching HTML: ' . $sUrl . ' - Message: ' . $e->getMessage()
            );
        }

        if ($response->getStatusCode() != 200) {
            throw new Exception\RuntimeException(
                'Failed fetching HTML: ' . $sUrl . ' - Message: ' . $response->getStatusCode(
                ) . ': ' . $response->getReasonPhrase()
            );
        }

        //Get body and set pointer to beginning of stream
        $body = $response->getBody();
        $body->rewind();

        return $body->getContents();
    }

    /**
     *
     * @return string
     * @throws \Exception
     * @psalm-suppress TooManyArguments
     */
    protected function getSiteUrl(): string
    {
        $oSiteMenu = $this->getSiteMenu();
        $oDefaultMenu = $oSiteMenu->getDefault();
        $app = self::getApplication();

        if (is_null($oDefaultMenu)) {
            $oCompParams = ComponentHelper::getParams('com_languages');
            $sLanguage = $app instanceof CMSApplication ? $oCompParams->get(
                'site',
                $app->get('language', 'en-GB')
            ) : 'en-GB';
            $oDefaultMenu = $oSiteMenu->getItems(['home', 'language'], [
                '1',
                $sLanguage
            ], true);
        }

        return $this->getMenuUrl($oDefaultMenu);
    }

    /**
     * @throws \Exception
     * @psalm-suppress TooManyArguments
     */
    protected function getSiteMenu(): AbstractMenu
    {
        /** @var CMSApplication $app */
        $app = self::getApplication();

        return $app->getMenu('site');
    }

    /**
     * @param MenuItem $oMenuItem
     * @return string
     * @psalm-suppress UndefinedMethod
     * @psalm-suppress UndefinedConstant
     */
    protected function getMenuUrl(MenuItem $oMenuItem): string
    {
        $sMenuUrl = $oMenuItem->link . '&Itemid=' . $oMenuItem->id;

        return Route::link('site', $sMenuUrl, true, 0, true);
    }

    /**
     * @throws \Exception
     */
    public function getMainMenuItemsHtmls($iLimit = 5, $bIncludeUrls = false): array
    {
        $oSiteMenu = $this->getSiteMenu();
        $oDefaultMenu = $oSiteMenu->getDefault();

        $aAttributes = [
            'menutype',
            'type',
            'level',
            'access',
            'home'
        ];

        $aValues = [
            $oDefaultMenu->menutype,
            'component',
            '1',
            '1',
            '0'
        ];

        //Only need 5 menu items including the home menu
        $aMenus = array_slice(
            array_merge([$oDefaultMenu], $oSiteMenu->getItems($aAttributes, $aValues)),
            0,
            $iLimit
        );

        $aHtmls = [];
        //Gonna limit the time spent on this
        $iTimerStart = microtime(true);
        /** @var MenuItem $oMenuItem */
        foreach ($aMenus as $oMenuItem) {
            $oMenuItem->link = $this->getMenuUrl($oMenuItem);

            try {
                if ($bIncludeUrls) {
                    $aHtmls[] = [
                        'url' => $oMenuItem->link,
                        'html' => $this->getHtml($oMenuItem->link)
                    ];
                } else {
                    $aHtmls[] = $this->getHtml($oMenuItem->link);
                }
            } catch (Exception\ExceptionInterface $e) {
                $this->logger->error($e->getMessage());
            }

            if (microtime(true) > $iTimerStart + 10.0) {
                break;
            }
        }

        return $aHtmls;
    }
}
