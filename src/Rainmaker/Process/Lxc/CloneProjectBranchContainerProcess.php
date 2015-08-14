<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Process for cloning a Linux Container used for a Rainmaker Project Branch.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class CloneProjectBranchContainerProcess extends Process {

  public function __construct(Container $newBranchContainer, Container $sourceBranchContainer, Container $project)
  {
    parent::__construct('lxc-attach -n ' . $project->getName() . ' -- lxc-clone -s -B btrfs ' . escapeshellarg($sourceBranchContainer->getName()) . ' ' . escapeshellarg($newBranchContainer->getName()));
  }

}
