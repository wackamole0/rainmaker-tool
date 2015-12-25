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
    $exports = Template::render('exports.twig', array(
      'exports' => $this->getExports()
    ));

    $exportsFile = $this->getFilesystem()->getFileContents('/etc/exports');
    $startMarker = '# Rainmaker - Start #';
    $endMarker = '# Rainmaker - End #';
    $count = 0;
    $exportsFile = preg_replace("/$startMarker(?:.+)$endMarker/s", $exports, $exportsFile, -1, $count);
    if ($count < 1) {
      $exportsFile .= "\n$exports\n";
    }
    $this->getFilesystem()->putFileContents('/etc/exports', $exportsFile);
  }

  /**
   * Returns an array of NFS exports.
   */
  protected function getExports()
  {
    $exports = array();
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $containers = $repository->getAllContainersOrderedByName();

    foreach ($containers as $container) {
      foreach ($container->getNfsExports($repository) as $export) {
        $exports[] = $export;
      }
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
