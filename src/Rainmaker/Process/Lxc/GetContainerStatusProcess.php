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
class GetContainerStatusProcess extends Process {

  public function __construct(Container $container)
  {
    parent::__construct('lxc-info -s -n ' . $container->getName());
  }

}
