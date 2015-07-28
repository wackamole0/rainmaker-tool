<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the ISC DHCP Server service in a Rainmaker environment.
 *
 * @package Rainmaker\ComponentManager
 */
class DhcpManager extends ComponentManager {

  /**
   * Creates the DHCPD configuration files for a project container.
   *
   * @param Container $container
   */
  public function createProjectDhcpSettings(Container $container, $reloadDhcpService = false)
  {
    $this->container = $container;
    $this->setProjectContainerDnsDefaults();
    $this->writeProjectDhcpHostFile();
    $this->writeDhcpHostIncludeFile();
    $this->writeDhcpClassFile();
    $this->writeDhcpClassIncludeFile();
    $this->writeDhcpSubnetFile();
    if ($reloadDhcpService) {
      $this->reloadDhcpService();
    }
  }

  /**
   * Creates the DHCPD configuration files for a project branch container.
   *
   * @param Container $container
   */
  public function createProjectBranchDhcpSettings(Container $container, $reloadDhcpService = false)
  {
    $this->container = $container;
    $this->setProjectBranchContainerDnsDefaults();
    $this->writeProjectBranchDhcpHostFile();
    $this->writeDhcpHostIncludeFile();
    if ($reloadDhcpService) {
      $this->reloadDhcpService();
    }
  }

  /**
   * Configures the default DHCP settings for a project container instance.
   */
  protected function setProjectContainerDnsDefaults()
  {
    if (empty($this->getContainer()->getNetworkAddress())) {
      $this->getContainer()->setNetworkAddress(
        $this->getEntityManager()->getRepository('Rainmaker:Container')->getNextAvailableNetwork());
    }

    if (empty($this->getContainer()->getIPAddress())) {
      $this->getContainer()->setIPAddress(
        $this->getEntityManager()->getRepository('Rainmaker:Container')->getNextAvailableNetworkHostAddress($this->getContainer()));
    }
  }

  /**
   * Configures the default DHCP settings for a project branch container instance.
   */
  protected function setProjectBranchContainerDnsDefaults()
  {
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());

    if (empty($this->getContainer()->getNetworkAddress())) {
      $this->getContainer()->setNetworkAddress($project->getNetworkAddress());
    }

    if (empty($this->getContainer()->getIPAddress())) {
      $this->getContainer()->setIPAddress($this->getEntityManager()->getRepository('Rainmaker:Container')->getNextAvailableNetworkHostAddress($project));
    }
  }

  /**
   * Writes the DHCPD configuration file specific to the Rainmaker project Linux container to the filesystem.
   */
  protected function writeProjectDhcpHostFile()
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
   * Writes the DHCPD configuration file specific to the Rainmaker project branch Linux container to the filesystem.
   */
  protected function writeProjectBranchDhcpHostFile()
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
   * Writes to the filesystem the DHCPD file which includes the DHCPD settings files for each specific container.
   */
  protected function writeDhcpHostIncludeFile()
  {
    $config = Template::render('dhcp/dhcpd.host.conf.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllContainersOrderedForHostsInclude()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the DHCPD configuration file specific to this group of Linux containers to the filesystem.
   */
  protected function writeDhcpClassFile()
  {
    $config = Template::render('dhcp/class.twig', array(
      'name' => $this->getContainer()->getName()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/' . $this->getContainer()->reverseDomain() . '.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes to the filesystem the DHCPD file which includes the DHCPD settings files for each class/group of containers.
   */
  protected function writeDhcpClassIncludeFile()
  {
    $config = Template::render('dhcp/dhcpd.class.conf.twig', array(
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllParentContainers()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the DHCPD configuration file for the subnet the containers reside in to the filesystem.
   */
  protected function writeDhcpSubnetFile()
  {
    $config = Template::render('dhcp/subnet.twig', array(
      'repo' => $this->getEntityManager()->getRepository('Rainmaker:Container'),
      'containers' => $this->getEntityManager()->getRepository('Rainmaker:Container')->getAllParentContainers()
    ));

    $file = '/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.subnet.conf.d/10.100.0.0.conf';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Reloads the DHCP service.
   */
  protected function reloadDhcpService()
  {
    try {
      $process = new Process('lxc-attach -n services -- service isc-dhcp-server restart');
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

}
