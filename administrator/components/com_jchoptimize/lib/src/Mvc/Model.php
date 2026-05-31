<?php

namespace JchOptimize\Core\Mvc;

use Joomla\DI\ContainerAwareInterface;
use JchOptimize\Core\Container\ContainerAwareTrait;
use _JchOptimizeVendor\Joomla\Model\DatabaseModelInterface;
use _JchOptimizeVendor\Joomla\Model\DatabaseModelTrait;
use _JchOptimizeVendor\Joomla\Model\StatefulModelInterface;
use _JchOptimizeVendor\Joomla\Model\StatefulModelTrait;

class Model implements ContainerAwareInterface, DatabaseModelInterface, StatefulModelInterface
{
    use ContainerAwareTrait;
    use DatabaseModelTrait;
    use StatefulModelTrait;
}
