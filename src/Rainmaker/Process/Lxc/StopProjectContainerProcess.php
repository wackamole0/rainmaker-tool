<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Stops a Linux Container used as a Rainmaker project.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class StopProjectContainerProcess extends Process
{

    public function __construct(Container $container)
    {
        parent::__construct('lxc-stop -n ' . $container->getName());
    }

}
