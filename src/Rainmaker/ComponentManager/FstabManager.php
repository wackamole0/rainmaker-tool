<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the Linux fstab in a Rainmaker environment
 *
 * @package Rainmaker\ComponentManager
 */
class FstabManager extends ComponentManager {

  /**
   * Updates the Linux fstab file with the the entries relevant to the given container
   *
   * @param Container $container
   */
  public function createProjectFstabEntries(Container $container, $mountFstabEntries = false)
  {
    $this->container = $container;
    $this->writeFstab();
    if ($mountFstabEntries) {
      $this->mountProjectFstabEntries();
    }
  }

  /**
   * Writes the Linux fstab file to disk
   */
  protected function writeFstab()
  {
    $config = Template::render('fstab.twig', array(
      'fstabToolMounts' => $this->getFstabToolMounts(),
      'fstabNfsMounts' => $this->getFstabNfsMounts()
    ));

    $this->getFilesystem()->putFileContents('/etc/fstab', $config);
  }

  /**
   * Mounts the fstab entries for the container that is currently being managed
   */
  protected function mountProjectFstabEntries()
  {
    $mount = $this->container->getFstabToolsMountPoint();
    $this->getProcessRunner();

    try {
      $process = new Process('mount ' . $mount['target']);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Returns an array of all the mount points which mount the Rainmaker LXC cache
   *
   * @return array
   */
  protected function getFstabToolMounts()
  {
    $mounts = array();
    foreach ($this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedForFstabToolMounts() as $container) {
      $mounts[] = $container->getFstabToolsMountPoint();
    }
    return $mounts;
  }

  /**
   * Returns an array of all the mount points for the NFS exports
   *
   * @return array
   */
  protected function getFstabNfsMounts()
  {
    $mounts = array();
    foreach ($this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedForFstabNfsMounts() as $container) {
      $mounts[] = $container->getFstabNfsMountPoint();
    }
    return $mounts;
  }

}
