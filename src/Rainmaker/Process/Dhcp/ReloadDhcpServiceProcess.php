<?php

namespace Rainmaker\Process\Dhcp;

use Symfony\Component\Process\Process;

/**
 * Process for reloading the DHCP service.
 *
 * @package Rainmaker\Process\Dhcp
 * @return void
 */
class ReloadDhcpServiceProcess extends Process {

  public function __construct()
  {
    parent::__construct('lxc-attach -n services -- service isc-dhcp-server restart');
  }

}
