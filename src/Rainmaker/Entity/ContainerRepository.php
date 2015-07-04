<?php

namespace Rainmaker\Entity;

use Doctrine\ORM\EntityRepository;

use Rainmaker\RainmakerException;

/**
 * Doctrine ORM EntityRepository for managing the Container entity
 */
class ContainerRepository extends EntityRepository
{
  protected $defaultNetworkPrefix         = '10.100';
  protected $defaultNetworkMin            = 1;
  protected $defaultNetworkMax            = 255;
  protected $defaultNetworkAddressMin     = 1;
  protected $defaultNetworkAddressMax     = 254;

  public function createContainer($name, $friendlyName)
  {
    $container = new Container();
    $container->setName($name);
    $container->setFriendlyName($friendlyName);
    $this->saveContainer($container);
    return $container;
  }

  public function saveContainer(Container $container)
  {
    $this->getEntityManager()->persist($container);
    $this->getEntityManager()->flush();
  }

  public function containerExists($name)
  {
    return NULL !== $this->findOneByName($name);
  }

  public function getAvailableNetworks()
  {
    return array_diff($this->getAllNetworks(), $this->getAllNetworksInUse());
  }

  public function getAllNetworks()
  {
    $networks = array();
    for($i = $this->defaultNetworkMin; $i <= $this->defaultNetworkMax; $i++) {
      $networks[] = $this->defaultNetworkPrefix . '.' . $i . '.0';
    }
    sort($networks);
    return $networks;
  }

  public function getAllNetworksInUse()
  {
    $networks = array();
    foreach ($this->getProjectParentContainers() as $projectContainer) {
      $networks[] = $projectContainer->getNetworkAddress();
    }
    sort($networks);
    return $networks;
  }

  public function getProjectParentContainers()
  {
    //@todo Next to execute a DB query here
    return array();
  }

  public function getNextAvailableNetworkAddress()
  {
    $availableNetworks = $this->getAvailableNetworks();
    return reset($availableNetworks);
  }

  public function getAvailableNetworkAddresses(Container $container)
  {
    return array_diff($this->getAllNetworkAddresses($container), $this->getAllNetworkAddressesInUse($container));
  }

  public function getAllNetworkAddresses(Container $container)
  {
    $network = $container->getNetworkAddress();
    $networkPrefix = NULL;
    if (FALSE !== ($lastdot = strripos($network, '.'))) {
      $networkPrefix = substr($network, 0, $lastdot);
    }
    $networkAddresses = array();
    for($i = $this->defaultNetworkAddressMin; $i <= $this->defaultNetworkAddressMax; $i++) {
      $networkAddresses[] = $networkPrefix . '.' . $i;
    }
    sort($networkAddresses);
    return $networkAddresses;
  }

  public function getAllNetworkAddressesInUse(Container $container)
  {
    $addresses = array();
    foreach ($this->getProjectBranchContainers($container) as $projectBranchContainer) {
      $addresses[] = $projectBranchContainer->getIPAddress();
    }
    sort($addresses);
    return $addresses;
  }

  public function getProjectBranchContainers(Container $container)
  {
    //@todo Next to execute a DB query here
    return array();
  }

  public function getNextAvailableIPAddress(Container $container)
  {
    $availableIps = $this->getAvailableNetworkAddresses($container);
    return reset($availableIps);
  }

  public function getAllContainersOrderedForHostsInclude() {
    //@todo Next to execute a DB query here
    return array();
  }

  public function getAllParentContainers() {
    //@todo Next to execute a DB query here
    return array();
  }

  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

}
