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
   * Removes the Linux fstab file entries relevant to the given project container.
   *
   * @param Container $container
   */
  public function removeProjectFstabEntries(Container $container)
  {
    $this->container = $container;
    $this->unmountProjectFstabEntries();
    $this->writeFstab();
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
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = $this->container->getMounts(null, $repository);
    foreach($mounts as $mount) {
      if (!$this->getFilesystem()->exists($mount->target)) {
        $this->getFilesystem()->mkdir($mount->target);
      }
    }
  }

  public function removeProjectBranchMountPoints()
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = $this->container->getMounts(null, $repository);
    foreach ($mounts as $mount) {
      if ($this->getFilesystem()->exists($mount->target)) {
        $this->getFilesystem()->remove($mount->target);
      }
    }
  }

  /**
   * Writes the Linux fstab file to disk.
   */
  protected function writeFstab()
  {
    $mounts = Template::render('fstab.twig', array(
      'fstabToolMounts' => $this->getFstabToolMounts(),
      'fstabNfsMounts' => $this->getFstabNfsMounts()
    ));

    $fstab = $this->getFilesystem()->getFileContents('/etc/fstab');
    $startMarker = '# Rainmaker - Start #';
    $endMarker = '# Rainmaker - End #';
    $count = 0;
    $fstab = preg_replace("/$startMarker(?:.+)$endMarker/s", $mounts, $fstab, -1, $count);
    if ($count < 1) {
      $fstab .= "\n$mounts\n";
    }
    $this->getFilesystem()->putFileContents('/etc/fstab', $fstab);
  }

  /**
   * Mounts the fstab entries for the container that is currently being managed.
   */
  protected function mountProjectFstabEntries()
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = $this->container->getMounts(null, $repository);
    $this->getProcessRunner();

    try {
      foreach ($mounts as $mount) {
        $process = new FstabMountProcess($mount->target);
        $this->getProcessRunner()->run($process);
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Unmounts the fstab entries for the container that is currently being managed.
   */
  protected function unmountProjectFstabEntries()
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = $this->container->getMounts(null, $repository);
    $this->getProcessRunner();

    try {
      foreach ($mounts as $mount) {
        $process = new FstabUnmountProcess($mount->target);
        $this->getProcessRunner()->run($process);
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Mounts the fstab entries for the container that is currently being managed.
   */
  protected function mountProjectBranchFstabEntries()
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = $this->container->getMounts(null, $repository);
    $this->getProcessRunner();

    try {
      foreach ($mounts as $mount) {
        $process = new FstabMountProcess($mount->target);
        $this->getProcessRunner()->run($process);
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Unmounts the fstab entries for the container that is currently being managed.
   */
  protected function unmountProjectBranchFstabEntries()
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = $this->container->getMounts(null, $repository);
    $this->getProcessRunner();

    try {
      foreach ($mounts as $mount) {
        $process = new FstabUnmountProcess($mount->target);
        $this->getProcessRunner()->run($process);
      }
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
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = array();
    foreach ($this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedByName() as $container) {
      foreach ($container->getMounts('bind', $repository) as $mount) {
        $mounts[] = $mount;
      }
    }
    return $mounts;
  }

  /**
   * Returns an array of all the mount points for the NFS exports.
   *
   * @return array
   */
  protected function getFstabNfsMounts()
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $mounts = array();
    foreach ($this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedByName() as $container) {
      foreach ($container->getMounts('nfs', $repository) as $mount) {
        $mounts[] = $mount;
      }
    }
    return $mounts;
  }

}
