<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Process\FstabMountProcess;
use Rainmaker\Process\FstabUnmountProcess;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the Linux fstab in a Rainmaker environment.
 *
 * @package Rainmaker\ComponentManager
 */
class FstabManager extends ComponentManager {

  /**
   * Updates the Linux fstab file with the entries relevant to the given project container.
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
   * Updates the Linux fstab file with the entries relevant to the given project branch container.
   *
   * @param Container $container
   */
  public function createProjectBranchFstabEntries(Container $container, $mountFstabEntries = false)
  {
    $this->container = $container;
    $this->checkAndCreateProjectBranchMountPoints();
    $this->writeFstab();
    if ($mountFstabEntries) {
      $this->mountProjectBranchFstabEntries();
    }
  }

  /**
   * Updates the Linux fstab file with the entries relevant to the given project branch container.
   *
   * @param Container $container
   */
  public function removeProjectBranchFstabEntries(Container $container)
  {
    $this->container = $container;
    $this->unmountProjectBranchFstabEntries();
    $this->writeFstab();
    $this->removeProjectBranchMountPoints();
  }

  public function checkAndCreateProjectBranchMountPoints()
  {
    foreach($this->getFstabNfsMounts() as $mount) {
      if (!$this->getFilesystem()->exists($mount['target'])) {
        $this->getFilesystem()->mkdir($mount['target']);
      }
    }
  }

  public function removeProjectBranchMountPoints()
  {
    $mount = $this->getEntityManager()->getRepository('Rainmaker:Container')->
      getFstabNfsMountPointForContainer($this->container);
    if ($this->getFilesystem()->exists($mount['target'])) {
      $this->getFilesystem()->remove($mount['target']);
    }
  }

  /**
   * Writes the Linux fstab file to disk.
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
   * Mounts the fstab entries for the container that is currently being managed.
   */
  protected function mountProjectFstabEntries()
  {
    $mount = $this->container->getFstabToolsMountPoint();
    $this->getProcessRunner();

    try {
      $process = new FstabMountProcess($mount['target']);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Mounts the fstab entries for the container that is currently being managed.
   */
  protected function mountProjectBranchFstabEntries()
  {
    $mount = $this->container->getFstabNfsMountPoint();
    $this->getProcessRunner();

    try {
      $process = new FstabMountProcess($mount['target']);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Unmounts the fstab entries for the container that is currently being managed.
   */
  protected function unmountProjectBranchFstabEntries()
  {
    $mount = $this->container->getFstabNfsMountPoint();
    $this->getProcessRunner();

    try {
      $process = new FstabUnmountProcess($mount['target']);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Returns an array of all the mount points which mount the Rainmaker LXC cache.
   *
   * @return array
   */
  protected function getFstabToolMounts()
  {
    return $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllFstabToolsMountPoint();
  }

  /**
   * Returns an array of all the mount points for the NFS exports.
   *
   * @return array
   */
  protected function getFstabNfsMounts()
  {
    return $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllFstabNfsMountPoint();
  }

}
