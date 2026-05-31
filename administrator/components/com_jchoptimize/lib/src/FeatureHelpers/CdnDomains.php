<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\FeatureHelpers;

use JchOptimize\Core\Cdn;
use JchOptimize\Core\Html\HtmlManager;
use Joomla\DI\Container;
use JchOptimize\Core\Registry;
use _JchOptimizeVendor\Laminas\EventManager\Event;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function defined;
use function implode;
use function trim;

defined('_JCH_EXEC') or die('Restricted access');
class CdnDomains extends \JchOptimize\Core\FeatureHelpers\AbstractFeatureHelper
{
    /**
     * @var Cdn
     */
    private Cdn $cdn;
    public function __construct(Container $container, Registry $params, Cdn $cdn)
    {
        parent::__construct($container, $params);
        $this->cdn = $cdn;
    }
    /**
     * @param array<string, array{domain:UriInterface, extensions:string}> $domains
     * @return void
     */
    public function addCdnDomains(array &$domains): void
    {
        /** @var string $domain2 */
        $domain2 = $this->params->get('pro_cookielessdomain_2', '');
        if (trim($domain2) != '') {
            /** @var string[] $staticFiles2Array */
            $staticFiles2Array = $this->params->get('pro_staticfiles_2', Cdn::getStaticFiles());
            $sStaticFiles2String = implode('|', $staticFiles2Array);
            $domains['domain2']['domain'] = $this->cdn->prepareDomain($domain2);
            $domains['domain2']['extensions'] = $sStaticFiles2String;
        }
        /** @var string $domain3 */
        $domain3 = $this->params->get('pro_cookielessdomain_3', '');
        if (trim($domain3) != '') {
            /** @var string[] $staticFiles3Array */
            $staticFiles3Array = $this->params->get('pro_staticfiles_3', Cdn::getStaticFiles());
            $sStaticFiles3String = implode('|', $staticFiles3Array);
            $domains['domain3']['domain'] = $this->cdn->prepareDomain($domain3);
            $domains['domain3']['extensions'] = $sStaticFiles3String;
        }
    }
}
