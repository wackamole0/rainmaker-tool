<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Process for creating a new Linux Container to be used for a Rainmaker Project Branch.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class CreateProjectBranchContainerProcess extends Process {

  public function __construct(Container $branch, Container $project)
  {
    $cmd = 'lxc-attach -n ' . $project->getName() . ' -- lxc-create --name ' . escapeshellarg($branch->getName()) . ' --bdev btrfs --template rainmaker-project-branch' .
      ' -- --profile ' . escapeshellarg($branch->getProfileName());
    if ($branch->isSetDownloadHost()) {
      $cmd .= ' --downloadhost ' . $branch->getDownloadHostFullyQualified();
    }
    parent::__construct($cmd);
  }

}
