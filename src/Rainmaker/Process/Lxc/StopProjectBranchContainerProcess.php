<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Stops a Linux Container used as a Rainmaker project branch.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class StopProjectBranchContainerProcess extends Process
{

    public function __construct(Container $branch, Container $project)
    {
        parent::__construct('lxc-attach -n ' . $project->getName() . ' -- lxc-stop -n ' . $branch->getName());
    }

}
