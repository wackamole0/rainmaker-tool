<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Process for creating a new Linux Container to be used for a Rainmaker Project.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class CreateProjectContainerProcess extends Process {

  public function __construct(Container $container)
  {
    $cmd = 'lxc-create --name ' . escapeshellarg($container->getName()) . ' --bdev btrfs --template rainmaker-project';
    if ($container->isSetDownloadHost()) {
      $cmd .= ' --downloadhost ' . $container->getDownloadHostFullyQualified();
    }
    parent::__construct($cmd);
  }

}
