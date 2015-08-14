<?php

namespace Rainmaker\Process\Lxc;

use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;

/**
 * Starts a Linux Container used as a Rainmaker project.
 *
 * @package Rainmaker\Process\Lxc
 * @return void
 */
class StartProjectContainerProcess extends Process {

  public function __construct(Container $container)
  {
    parent::__construct('lxc-start -d -n ' . $container->getName());
  }

}
