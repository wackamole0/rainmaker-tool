<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Process\Nfs\ReloadNfsServiceProcess;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the Linux NFS service in a Rainmaker environment.
 *
 * @package Rainmaker\ComponentManager
 */
class NfsManager extends ComponentManager {

  /**
   * Updates the Linux NFS exports file with the entries relevant to the given project container.
   *
   * @param Container $container
   */
  public function createProjectBranchEntries(Container $container, $reloadService = false)
  {
    $this->container = $container;
    $this->writeExportsFile();
    if ($reloadService) {
      $this->reloadService();
    }
  }

  /**
   * Writes the Linux NFS exports file to disk.
   */
  protected function writeExportsFile()
  {
    $config = Template::render('exports.twig', array(
      'exports' => $this->getExports()
    ));

    $this->getFilesystem()->putFileContents('/etc/exports', $config);
  }

  /**
   * Returns an array of NFS exports.
   */
  protected function getExports()
  {
    $exports = array();
    $branches = $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllProjectBranchContainers();

    foreach ($branches as $branch) {
      $exports[] = '/export/rainmaker/' . $branch->getName();
    }

    return $exports;
  }

  /**
   * Reloads the NFS service.
   */
  protected function reloadService()
  {
    try {
      $process = new ReloadNfsServiceProcess();
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

}
