<?php

namespace Rainmaker\ComponentManager;

use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the BIND9 DNS service in a Rainmaker environment
 *
 * @package Rainmaker\ComponentManager
 */
class BindManager extends ComponentManager {

  /**
   * Create an new Linux container for the given abstract container
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function configureDnsZoneForProjectContainer(Container $container)
  {
    $this->container = $container;

    $this->setContainerDnsDefaults();
    $this->writeDnsZoneFile();
    $this->writeDnsZonePtrFile();
    $this->writeRainmakerDbFile();
    $this->writeBindLocalConfFile();
  }

  /**
   * Configures the default DNS zone settings for a container instance
   */
  protected function setContainerDnsDefaults()
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
  }

  /**
   * Writes the DNS zone file for the Linux container to the filesystem
   */
  protected function writeDnsZoneFile()
  {
    $config = Template::render('bind/zone.twig', array(
      'container' => $this->getContainer(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->getDomain();
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the DNS zone PTR file for the Linux container to the filesystem
   */
  protected function writeDnsZonePtrFile()
  {
    $config = Template::render('bind/ptr-zone.twig', array(
      'container' => $this->getContainer(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $this->getContainer()->networkPrefix();
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the Rainmaker Bind DB file for the Linux container to the filesystem
   */
  protected function writeRainmakerDbFile()
  {
    $config = Template::render('bind/db.rainmaker.twig', array(
      'container' => $this->getContainer(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/named.conf.rainmaker/' . $this->getContainer()->getName() . '.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the local Bind named configuration file to the filesystem
   */
  protected function writeBindLocalConfFile()
  {
    $config = Template::render('bind/named.conf.local.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllParentContainers(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/bind/named.conf.local';
    $this->getFilesystem()->putFileContents($file, $config);
  }

}
