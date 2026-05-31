<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2021 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Controller;

use JchOptimize\Core\Admin\Ajax\Ajax as AdminAjax;
use JchOptimize\Core\Mvc\Controller;
use Joomla\CMS\Application\AdministratorApplication;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class OptimizeImage extends Controller
{
    public function execute(): bool
    {
        /** @var AdministratorApplication $app */
        $app = $this->getApplication();

        AdminAjax::getInstance('OptimizeImage')->run();

        $app->close();
        return true;
    }
}
