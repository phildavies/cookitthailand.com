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

use JchOptimize\Core\Admin\Ajax\Ajax as AdminAjax;
use JchOptimize\Core\Mvc\Controller;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\Input\Input;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class Ajax extends Controller
{
    /**
     * @var string[]
     */
    private $taskMap;

    /**
     * @param Input $input
     * @param AdministratorApplication $app
     */
    public function __construct(Input $input, AdministratorApplication $app)
    {
        parent::__construct($input, $app);

        $this->taskMap = [
            'filetree' => 'doFileTree',
            'multiselect' => 'doMultiSelect',
            'optimizeimage' => 'doOptimizeImage',
            'smartcombine' => 'doSmartCombine',
            'garbagecron' => 'doGarbageCron'
        ];
    }

    public function execute(): bool
    {
        /** @var Input $input */
        $input = $this->getInput();
        /** @var string $task */
        $task = $input->get('task');

        /** @see self::doFileTree() */
        /** @see self::doMultiSelect() */
        /** @see self::doOptimizeImage() */
        /** @see self::doSmartCombine() */
        /** @see self::doGarbageCron() */
        $this->{$this->taskMap[$task]}();

        /** @var AdministratorApplication $app */
        $app = $this->getApplication();
        $app->close();

        return true;
    }

    private function doFileTree(): void
    {
        echo AdminAjax::getInstance('FileTree')->run();
    }

    private function doMultiSelect(): void
    {
        echo AdminAjax::getInstance('MultiSelect')->run();
    }

    private function doOptimizeImage(): void
    {
        echo AdminAjax::getInstance('OptimizeImage')->run();
    }

    private function doSmartCombine(): void
    {
        echo AdminAjax::getInstance('SmartCombine')->run();
    }

    private function doGarbageCron(): void
    {
        echo AdminAjax::getInstance('GarbageCron')->run();
    }
}
