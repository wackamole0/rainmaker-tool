<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Process for destroying a Linux Container used for a Rainmaker Project Branch.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class DestroyProjectBranchContainerProcess extends Process
{

    public function __construct(Container $branch, Container $project)
    {
        $cmd = 'lxc-attach -n ' . $project->getName() . ' -- lxc-destroy --name ' . escapeshellarg($branch->getName());
        parent::__construct($cmd);
    }

}
