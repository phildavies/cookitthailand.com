<?php

namespace JchOptimize\Core\Container;

use Joomla\DI\Exception\ContainerNotFoundException;
use Joomla\DI\Container;

trait ContainerAwareTrait
{
    private ?Container $container = null;
    protected function getContainer(): Container
    {
        if ($this->container instanceof Container) {
            return $this->container;
        }
        throw new ContainerNotFoundException('Container not set in ' . \get_class($this));
    }
    public function setContainer(Container $container): static
    {
        $this->container = $container;
        return $this;
    }
}
