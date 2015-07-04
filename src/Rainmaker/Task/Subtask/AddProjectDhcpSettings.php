<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\DhcpManager;

/**
 * Configure the DHCP settings for the container
 *
 * @package Rainmaker\Task\Subtask
 */
class AddProjectDhcpSettings extends Task
{

  public function performTask()
  {
    // Set the network
    $this->getContainer()->setNetworkAddress(
      $this->getEntityManager()->getRepository('Rainmaker:Container')->getNextAvailableNetworkAddress());
    // Set the IP addr
    $this->getContainer()->setIPAddress(
      $this->getEntityManager()->getRepository('Rainmaker:Container')->getNextAvailableIPAddress($this->getContainer()));

    $dhcp = new DhcpManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $dhcp->createProjectDhcpSettings($this->getContainer());
  }

}
