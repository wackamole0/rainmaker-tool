<?php

namespace Rainmaker\ComponentManager;

use Rainmaker\Util\Template;
use \Rainmaker\Entity\Container;

/**
 * A class for managing the ISC DHCP Server service in a Rainmaker environment
 *
 * @package Rainmaker\ComponentManager
 */
class DhcpManager extends ComponentManager {

  /**
   * Creates the DHCPD configuration files for a project container
   *
   * @param Container $container
   */
  public function createProjectDhcpSettings(Container $container)
  {
    $this->container = $container;
    $this->writeDhcpHostFile();
    $this->writeDhcpHostIncludeFile();
    $this->writeDhcpClassFile();
    $this->writeDhcpClassIncludeFile();
    $this->writeDhcpSubnetFile();
  }

  /**
   * Writes the DHCPD configuration file specific to the Linux container to the filesystem
   */
  protected function writeDhcpHostFile()
  {
    $config = Template::render('dhcp/host.twig', array(
      'fqdn' => $this->getContainer()->getHostname(),
      'hwaddr' => $this->getContainer()->getLxcHwAddr(),
      'ipaddr' => $this->getContainer()->getIPAddress()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/' . $this->getContainer()->reverseHostname() . '.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes to the filesystem the DHCPD file which includes the DHCPD settings files for each specific container
   */
  protected function writeDhcpHostIncludeFile()
  {
//    $containers = $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedForHostsInclude();
//    var_dump($containers);
    $config = Template::render('dhcp/dhcpd.host.conf.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedForHostsInclude(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the DHCPD configuration file specific to this group of Linux containers to the filesystem
   */
  protected function writeDhcpClassFile()
  {
    $config = Template::render('dhcp/class.twig', array(
      'name' => $this->getContainer()->getName(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/localdev.' . $this->getContainer()->getName() . '.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes to the filesystem the DHCPD file which includes the DHCPD settings files for each class/group of containers
   */
  protected function writeDhcpClassIncludeFile()
  {
    $config = Template::render('dhcp/dhcpd.class.conf.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllParentContainers(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the DHCPD configuration file for the subnet the containers reside in to the filesystem
   */
  protected function writeDhcpSubnetFile()
  {
    $config = Template::render('dhcp/subnet.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllParentContainers(),
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.subnet.conf.d/10.100.0.0.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

}
