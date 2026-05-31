<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2023 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Controller;

use JchOptimize\Core\Mvc\Controller;
use JchOptimize\Model\Cache;
use Joomla\Application\AbstractApplication;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\Input\Input;

use function json_encode;

class CacheInfo extends Controller
{
    private Cache $cacheModel;

    public function __construct(Cache $cacheModel, ?Input $input, ?AbstractApplication $app)
    {
        $this->cacheModel = $cacheModel;

        parent::__construct($input, $app);
    }

    public function execute(): bool
    {
        /** @var AdministratorApplication $app */
        $app = $this->getApplication();

        [$size, $numFiles] = $this->cacheModel->getCacheSize();

        $body = json_encode([
            'size' => $size,
            'numFiles' => $numFiles
        ]);

        $app->clearHeaders();
        $app->setHeader('Content-Type', 'application/json');
        $app->setHeader('Content-Length', (string)strlen($body));
        $app->setBody($body);
        $app->allowCache(false);

        echo $app->toString();

        $app->close();

        return true;
    }
}
