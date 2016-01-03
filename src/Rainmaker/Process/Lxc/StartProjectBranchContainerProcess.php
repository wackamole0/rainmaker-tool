<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Starts a Linux Container used as a Rainmaker project branch.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class StartProjectBranchContainerProcess extends Process
{

    public function __construct(Container $branch, Container $project)
    {
        parent::__construct('lxc-attach -n ' . $project->getName() . ' -- lxc-start -d -n ' . $branch->getName());
    }

}
