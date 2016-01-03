<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\Util\Template;

/**
 * Configure the Linux project host.
 *
 * @package Rainmaker\Task\Subtask
 */
class ConfigureProjectBranchLinuxHost extends Task
{

    public function performTask()
    {
        $this->writeHostnameFile();
        $this->writeHostsFile();
    }

    protected function writeHostnameFile()
    {
        $config = Template::render('lxc/project-hostname.twig', array(
            'hostname' => $this->getContainer()->getHostname()
        ));

        $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/rootfs/etc/hostname';
        if ($this->getContainer()->isProjectBranch()) {
            $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());
            $file = '/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $this->getContainer()->getName() . '/rootfs/etc/hostname';
        }
        $this->getFilesystem()->putFileContents($file, $config);
    }

    protected function writeHostsFile()
    {
        $config = Template::render('lxc/project-hosts.twig', array(
            'hostname' => $this->getContainer()->getHostname()
        ));

        $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/rootfs/etc/hosts';
        if ($this->getContainer()->isProjectBranch()) {
            $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());
            $file = '/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $this->getContainer()->getName() . '/rootfs/etc/hosts';
        }
        $this->getFilesystem()->putFileContents($file, $config);
    }

}
