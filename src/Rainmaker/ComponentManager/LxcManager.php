<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the Linux Containers (LXC) in a Rainmaker environment
 *
 * @package Rainmaker\ComponentManager
 */
class LxcManager extends ComponentManager {

  /**
   * Create an new Linux container for the given abstract container
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function createContainer(Container $container)
  {
    $this->container = $container;

    try {
      $process = new Process('lxc-clone _golden-proj_ ' . $this->getContainer()->getName());
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Creates the configuration file for the Linux container
   *
   * @param Container $container
   */
  public function configureContainer(Container $container)
  {
    $this->container = $container;

    $this->getContainer()->setLxcUtsName($this->getLxcConfigurationSettingValue('lxc.utsname'));
    $this->getContainer()->setLxcHwAddr($this->getLxcConfigurationSettingValue('lxc.network.hwaddr'));
    $this->getContainer()->setLxcRootFs($this->getLxcConfigurationSettingValue('lxc.rootfs'));

    $this->writeLxcConfigurationFile();
  }

  /**
   * Extracts the value of setting from a Linux container configuration file
   *
   * @param $setting
   * @return null
   */
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

  /**
   * Writes the configuration file for the Linux container to the filesystem
   */
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
