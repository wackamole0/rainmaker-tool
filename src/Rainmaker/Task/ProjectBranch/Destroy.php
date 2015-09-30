<?php

namespace Rainmaker\Task\ProjectBranch;

use Rainmaker\Task\TaskWithSubtasks;
use Rainmaker\RainmakerException;
use Rainmaker\Entity\Container;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Destroys a Rainmaker project branch container.
 *
 * @package Rainmaker\Task\ProjectBranch
 */
class Destroy extends TaskWithSubtasks {

  public function getSubtasks()
  {
    $subtasks = array(
      // Configure Nfs
      new \Rainmaker\Task\Subtask\RemoveProjectBranchNfsEntries(),

      // Configure fstab
      new \Rainmaker\Task\Subtask\RemoveProjectBranchFstabEntries(),

      // Configure Bind
      new \Rainmaker\Task\Subtask\RemoveProjectBranchDnsSettings(),

      // Configure DHCP
      new \Rainmaker\Task\Subtask\RemoveProjectBranchDhcpSettings(),

      // Remove container
      new \Rainmaker\Task\Subtask\DestroyProjectBranchLinuxContainer(),
    );

    return $subtasks;
  }

  public function performTask()
  {
    if ($this->getContainer()->isRunning()) {
      throw new RainmakerException('The container is running. It must be stopped before it can be destroyed.');
    }

    $this->log(\Monolog\Logger::DEBUG, 'Destroying container [' . $this->getContainer()->getName() . ']');

    try {
      $this->getContainer()->setState(Container::STATE_DESTROYING);
      parent::performTask();
    } catch (RainmakerException $e) {
      throw $e;
    }

    $this->getEntityManager()->getRepository('Rainmaker:Container')->removeContainer($this->getContainer());
  }

}
