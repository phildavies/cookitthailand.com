<?php
/**
 * @package     JchOptimize\Core\Service
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace JchOptimize\Service;

use JchOptimize\Controller\ModeSwitcher as ModeSwitcherController;
use JchOptimize\Core\Container\Container;
use JchOptimize\Core\Container\ServiceProviderInterface;
use JchOptimize\Core\Registry;
use JchOptimize\Model\Cache;
use JchOptimize\Model\ModeSwitcher as ModeSwitcherModel;
use JchOptimize\Model\TogglePlugins;
use Joomla\Application\AbstractApplication;
use Joomla\Database\DatabaseInterface;
use Joomla\Input\Input;

use function defined;

defined('_JCH_EXEC') or die('Restricted access');

class ModeSwitcherProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        if (!$container->has(ModeSwitcherController::class)) {
            $container->alias('ModeSwitcher', ModeSwitcherController::class)
                ->share(ModeSwitcherController::class, [$this, 'getControllerModeSwitcherService']);
        }
        if (!$container->has(ModeSwitcherModel::class)) {
            $container->share(ModeSwitcherModel::class, [$this, 'getModelModeSwitcherService']);
        }
    }

    public function getControllerModeSwitcherService(Container $container): ModeSwitcherController
    {
        return new ModeSwitcherController(
            $container->get(ModeSwitcherModel::class),
            $container->get(Input::class),
            $container->get(AbstractApplication::class)
        );
    }

    public function getModelModeSwitcherService(Container $container): ModeSwitcherModel
    {
        $model = new ModeSwitcherModel(
            $container->get(Registry::class),
            $container->get(Cache::class),
            $container->get(TogglePlugins::class)
        );
        $model->setDb($container->get(DatabaseInterface::class));

        return $model;
    }
}
