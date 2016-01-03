<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\TaskWithSubtasks;
use Rainmaker\RainmakerException;
use Rainmaker\Entity\Container;

/**
 * Destroys a Rainmaker project container.
 *
 * @package Rainmaker\Task\Project
 */
class Destroy extends TaskWithSubtasks
{

    public function getSubtasks()
    {
        $subtasks = array(
            // Configure fstab
            new \Rainmaker\Task\Subtask\RemoveProjectFstabEntries(),

            // Configure Bind
            new \Rainmaker\Task\Subtask\RemoveProjectDnsSettings(),

            // Configure DHCP
            new \Rainmaker\Task\Subtask\RemoveProjectDhcpSettings(),

            // Remove container
            new \Rainmaker\Task\Subtask\DestroyProjectLinuxContainer(),
        );

        return $subtasks;
    }

    public function performTask()
    {
        if ($this->getContainer()->isRunning()) {
            throw new RainmakerException('The container is running. It must be stopped before it can be destroyed.');
        }

        if (count($this->getEntityManager()->getRepository('Rainmaker:Container')->getProjectBranchContainers($this->getContainer())) > 0) {
            throw new RainmakerException('The container cannot be desroyed while it still has subcontainers configured.');
        }

        $this->log(\Monolog\Logger::DEBUG, 'Destroying container [' . $this->getContainer()->getName() . ']');

        try {
            $this->getContainer()->setState(Container::STATE_DESTROYING);
            $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());
            parent::performTask();
        } catch (RainmakerException $e) {
            throw $e;
        }

        $this->getEntityManager()->getRepository('Rainmaker:Container')->removeContainer($this->getContainer());
    }

}
