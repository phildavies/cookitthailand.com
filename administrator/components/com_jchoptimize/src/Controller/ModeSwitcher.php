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

namespace JchOptimize\Controller;

use JchOptimize\Core\Mvc\Controller;
use JchOptimize\Model\ModeSwitcher as ModeSwitcherModel;
use Joomla\Application\AbstractApplication;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\Input\Input;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class ModeSwitcher extends Controller
{
    /**
     * @var ModeSwitcherModel
     */
    private ModeSwitcherModel $model;

    public function __construct(
        ModeSwitcherModel    $model,
        ?Input               $input = null,
        ?AbstractApplication $application = null
    ) {
        $this->model = $model;

        parent::__construct($input, $application);
    }

    /**
     * @return bool
     */
    public function execute()
    {
        /** @var Input $input */
        $input = $this->getInput();
        /** @var string $action */
        $action = $input->get('task');
        $this->model->{$action}();
        $mode = str_replace('set', '', $action);

        /** @var AdministratorApplication $app */
        $app = $this->getApplication();
        $app->enqueueMessage(sprintf('JCH Optimize set in %s mode', $mode));
        $app->redirect(base64_decode((string)$input->get('return', '', 'base64')));

        return true;
    }
}
