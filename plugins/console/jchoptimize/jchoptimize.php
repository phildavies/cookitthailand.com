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

use JchOptimize\Command\ReCache;
use Joomla\Application\ApplicationEvents;
use Joomla\Application\Event\ApplicationEvent;
use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\Console\Loader\WritableLoaderInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_jchoptimize/autoload.php';

class plgConsoleJchoptimize extends CMSPlugin implements SubscriberInterface
{
    private array $registeredCommands = [
            ReCache::class => 'jchoptimize:recache'
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            /** @see self::registerCommands() */
                ApplicationEvents::BEFORE_EXECUTE => 'registerCommands',
        ];
    }

    public function registerCommands(ApplicationEvent $event): void
    {
        /** @var ConsoleApplication $cliApp */
        $cliApp = $event->getApplication();

        //load language
        $lang = $cliApp->getLanguage();
        $lang->load('com_jchoptimize', JPATH_ADMINISTRATOR);

        $container = Factory::getContainer();

        foreach ($this->registeredCommands as $id => $command) {
            $container->share(
                $id,
                function (Psr\Container\ContainerInterface $container) use ($id) {
                    return new $id();
                },
                true
            );

            $container->get(WritableLoaderInterface::class)->add($command, $id);
        }
    }
}
