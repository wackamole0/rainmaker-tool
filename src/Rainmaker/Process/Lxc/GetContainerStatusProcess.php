<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Returns the current status of a Linux Container.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class GetContainerStatusProcess extends Process
{

    public function __construct(Container $container, Container $parent = null)
    {
        $cmd = 'lxc-info -s -n ' . $container->getName();
        if (!empty($parent)) {
            $cmd = 'lxc-attach -n ' . $parent->getName() . ' -- ' . $cmd;
        }
        parent::__construct($cmd);
    }

}
