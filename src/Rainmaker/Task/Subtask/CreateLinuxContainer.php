<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\Util\Template;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Creates a Linux container and configures that container config file for a Rainmaker project
 *
 * @package Rainmaker\Task\Subtask
 */
class CreateLinuxContainer extends Task
{

  public function performTask()
  {
    try {
      $process = new Process('lxc-clone _golden-proj_ ' . $this->getContainer()->getName());
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }

    $this->getContainer()->setLxcUtsName($this->getLxcConfigurationSettingValue('lxc.utsname'));
    $this->getContainer()->setLxcHwAddr($this->getLxcConfigurationSettingValue('lxc.network.hwaddr'));
    $this->getContainer()->setLxcRootFs($this->getLxcConfigurationSettingValue('lxc.rootfs'));

    $this->writeLxcConfigurationFile();

  }

  protected function getLxcConfigurationSettingValue($setting)
  {
    $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/config';
    $contents = $this->getFilesystem()->getFileContents($file);
    $matches = array();
    if (preg_match('/\s*' . $setting . '\s*=\s*(.+)\s*/', $contents, $matches) !== 1) {
      return null;
    }

    return $matches[1];
  }

  protected function writeLxcConfigurationFile()
  {
    $config = Template::render('lxc/project-config.twig', array(
      'lxc_root_fs'       => $this->getContainer()->getLxcRootFs(),
      'lxc_utsname'       => $this->getContainer()->getLxcUtsName(),
      'lxc_net_hwaddr'    => $this->getContainer()->getLxcHwAddr(),
    ));

    $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/config';
    $this->getFilesystem()->putFileContents($file, $config);
  }

}