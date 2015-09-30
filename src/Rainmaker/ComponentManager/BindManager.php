<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Process\Bind\ReloadBindServiceProcess;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the BIND9 DNS service in a Rainmaker environment.
 *
 * @package Rainmaker\ComponentManager
 */
class BindManager extends ComponentManager {

  /**
   * Configures the DNS zones files for a Rainmaker project Linux container.
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function configureDnsZoneForProjectContainer(Container $container, $reloadBindService = false)
  {
    $this->container = $container;

    $this->setProjectContainerDnsDefaults();
    $this->writeProjectDnsZoneFile();
    $this->writeProjectDnsZonePtrFile();
    $this->writeRainmakerDbFile();
    $this->writeBindLocalConfFile();
    if ($reloadBindService) {
      $this->reloadBindService();
    }
  }

  /**
   * Removes the DNS zones files for a Rainmaker project Linux container.
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function removeDnsZoneForProjectContainer(Container $container, $reloadBindService = false)
  {
    $this->container = $container;

    $this->removeProjectDnsZoneFile();
    $this->removeProjectDnsZonePtrFile();
    $this->writeRainmakerDbFile();
    $this->writeBindLocalConfFile();
    if ($reloadBindService) {
      $this->reloadBindService();
    }
  }

  /**
   * Configures the DNS zones files for a Rainmaker project branch Linux container.
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function configureDnsZoneForProjectBranchContainer(Container $container, $reloadBindService = false)
  {
    $this->container = $container;

    $this->setProjectBranchContainerDnsDefaults();
    $this->writeProjectBranchDnsZoneFile();
    $this->writeProjectBranchDnsZonePtrFile();
    if ($reloadBindService) {
      $this->reloadBindService();
    }
  }

  /**
   * Removes the DNS zones files for a Rainmaker project branch Linux container.
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function removeDnsZoneForProjectBranchContainer(Container $container, $reloadBindService = false)
  {
    $this->container = $container;

    $this->writeProjectBranchDnsZoneFile();
    $this->writeProjectBranchDnsZonePtrFile();
    if ($reloadBindService) {
      $this->reloadBindService();
    }
  }

  /**
   * Configures the default DNS zone settings for a Rainmaker project container instance.
   */
  protected function setProjectContainerDnsDefaults()
  {
    if (empty($this->getContainer()->getDnsZoneTtl())) {
      $this->getContainer()->setDnsZoneTtl(604800);
    }

    if (empty($this->getContainer()->getDnsZonePriMasterNs())) {
      //@todo Proper implementation needed
      $this->getContainer()->setDnsZonePriMasterNs('ns.rainmaker.localdev');
    }

    if (empty($this->getContainer()->getDnsZoneAdminEmail())) {
      //@todo Proper implementation needed
      $this->getContainer()->setDnsZoneAdminEmail('hostmaster.rainmaker.localdev');
    }

    if (empty($this->getContainer()->getDnsZoneSerial())) {
      $this->getContainer()->setDnsZoneSerial(date('Ymd') . '01');
    }

    if (empty($this->getContainer()->getDnsZoneRefresh())) {
      $this->getContainer()->setDnsZoneRefresh(604800);
    }

    if (empty($this->getContainer()->getDnsZoneRetry())) {
      $this->getContainer()->setDnsZoneRetry(86400);
    }

    if (empty($this->getContainer()->getDnsZoneExpire())) {
      $this->getContainer()->setDnsZoneExpire(2419200);
    }

    if (empty($this->getContainer()->getDnsZoneNegCacheTtl())) {
      $this->getContainer()->setDnsZoneNegCacheTtl(604800);
    }

    $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());
  }

  /**
   * Configures the default DNS zone settings for a Rainmaker project branch container instance.
   */
  protected function setProjectBranchContainerDnsDefaults()
  {

  }

  /**
   * Writes the DNS zone file for the Rainmaker project Linux container to the filesystem.
   */
  protected function writeProjectDnsZoneFile()
  {
    $config = Template::render('bind/zone.twig', array(
      'repo' => $this->getEntityManager()->getRepository('Rainmaker:Container'),
      'container' => $this->getContainer()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->getDomain();
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Removes the DNS zone file for the Rainmaker project Linux container to the filesystem.
   */
  protected function removeProjectDnsZoneFile()
  {
    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->getDomain();
    $this->getFilesystem()->remove($file);
  }

  /**
   * Writes the DNS zone file for the Rainmaker project branch Linux container to the filesystem.
   */
  protected function writeProjectBranchDnsZoneFile()
  {
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());
    $config = Template::render('bind/zone.twig', array(
      'repo' => $this->getEntityManager()->getRepository('Rainmaker:Container'),
      'container' => $project
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $project->getDomain();
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the DNS zone PTR file for the Rainmaker project Linux container to the filesystem.
   */
  protected function writeProjectDnsZonePtrFile()
  {
    $config = Template::render('bind/ptr-zone.twig', array(
      'repo' => $this->getEntityManager()->getRepository('Rainmaker:Container'),
      'container' => $this->getContainer()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->networkPrefix();
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Removes the DNS zone PTR file for the Rainmaker project Linux container to the filesystem.
   */
  protected function removeProjectDnsZonePtrFile()
  {
    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->networkPrefix();
    $this->getFilesystem()->remove($file);
  }

  /**
   * Writes the DNS zone PTR file for the Rainmaker project branch Linux container to the filesystem.
   */
  protected function writeProjectBranchDnsZonePtrFile()
  {
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());
    $config = Template::render('bind/ptr-zone.twig', array(
      'repo' => $this->getEntityManager()->getRepository('Rainmaker:Container'),
      'container' => $project
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->networkPrefix();
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the Rainmaker Bind DB file for the Linux container to the filesystem.
   */
  protected function writeRainmakerDbFile()
  {
    $config = Template::render('bind/db.rainmaker.twig', array(
      'container' => $this->getContainer()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/named.conf.rainmaker/' . $this->getContainer()->getName() . '.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the local Bind named configuration file to the filesystem.
   */
  protected function writeBindLocalConfFile()
  {
    $config = Template::render('bind/named.conf.local.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllParentContainers(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/named.conf.local';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Reloads the Bind service.
   */
  protected function reloadBindService()
  {
    try {
      $process = new ReloadBindServiceProcess();
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

}
