<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Process for destroying a Linux Container used for a Rainmaker Project.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class DestroyProjectContainerProcess extends Process
{

    public function __construct(Container $project)
    {
        $cmd = 'lxc-destroy --name ' . escapeshellarg($project->getName());
        parent::__construct($cmd);
    }

}
