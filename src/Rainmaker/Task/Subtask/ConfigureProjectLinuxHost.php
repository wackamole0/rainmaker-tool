<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\Util\Template;

/**
 * Configure the Linux project host.
 *
 * @package Rainmaker\Task\Subtask
 */
class ConfigureProjectLinuxHost extends Task
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
        $this->getFilesystem()->putFileContents($file, $config);
    }

    protected function writeHostsFile()
    {
        $config = Template::render('lxc/project-hosts.twig', array(
            'hostname' => $this->getContainer()->getHostname()
        ));

        $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/rootfs/etc/hosts';
        $this->getFilesystem()->putFileContents($file, $config);
    }

}
