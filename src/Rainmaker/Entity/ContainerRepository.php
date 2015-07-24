<?php

namespace Rainmaker\Entity;

use Doctrine\ORM\EntityRepository;

use Rainmaker\RainmakerException;

/**
 * Doctrine ORM EntityRepository for managing the Container entity
 */
class ContainerRepository extends EntityRepository
{
  protected $defaultNetworkPrefix             = '10.100';
  protected $defaultNetworkMin                = 1;
  protected $defaultNetworkMax                = 255;
  protected $defaultNetworkHostAddressMin     = 1;
  protected $defaultNetworkHostAddressMax     = 254;

  public function createContainer($name, $friendlyName = '', $persist = false)
  {
    $container = new Container();
    $container->setName($name);
    $container->setFriendlyName($friendlyName);
    if ($persist) {
      $this->saveContainer($container);
    }
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

  public function getAllParentContainers() {
    return $this->createQueryBuilder('c')
      ->where('c.parentId IS NULL')
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  public function getProjectParentContainers()
  {
    //@todo Can we do away with this alias?
    return $this->getAllParentContainers();
  }

  public function getProjectBranchContainers(Container $container)
  {
    return $this->createQueryBuilder('c')
      ->where('c.parentId = :parentId')
      ->setParameter('parentId', $container->getId())
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  // Network and IP address methods

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

  public function getNextAvailableNetwork()
  {
    $availableNetworks = $this->getAvailableNetworks();
    return reset($availableNetworks);
  }

  public function getAvailableNetworkHostAddresses(Container $container)
  {
    return array_diff($this->getAllNetworkHostAddresses($container), $this->getAllNetworkHostAddressesInUse($container));
  }

  public function getAllNetworkHostAddresses(Container $container)
  {
    $networkPrefix = $container->networkPrefix();
    $networkHostAddresses = array();
    for($i = $this->defaultNetworkHostAddressMin; $i <= $this->defaultNetworkHostAddressMax; $i++) {
      $networkHostAddresses[] = $networkPrefix . '.' . $i;
    }
    sort($networkHostAddresses);
    return $networkHostAddresses;
  }

  public function getAllNetworkHostAddressesInUse(Container $container)
  {
    $addresses = array();
    foreach ($this->getProjectBranchContainers($container) as $projectBranchContainer) {
      $addresses[] = $projectBranchContainer->getIPAddress();
    }
    sort($addresses);
    return $addresses;
  }

  public function getNextAvailableNetworkHostAddress(Container $container)
  {
    $availableIps = $this->getAvailableNetworkHostAddresses($container);
    return reset($availableIps);
  }

  /**
   * @return string
   */
  public function getNetworkHostAddrRangeMin(Container $container)
  {
    $project = $this->getParentContainer($container);
    return $project->networkPrefix() . '.' . $this->defaultNetworkHostAddressMin;
  }

  /**
   * @return string
   */
  public function getNetworkHostAddrRangeMax(Container $container)
  {
    $project = $this->getParentContainer($container);
    return $project->networkPrefix() . '.' . $this->defaultNetworkHostAddressMax;
  }

  // DHCP methods

  public function getAllContainersOrderedForHostsInclude() {
    return $this->createQueryBuilder('c')
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  // DNS methods

  /**
   * @return array
   */
  public function getPrimaryNameServers(Container $container)
  {
    $project = $this->getParentContainer($container);
    return array(
      'ns.rainmaker.localdev',
      'ns.' . $project->getDomain()
    );
  }

  /**
   * @return array
   */
  public function getNameServerRecords(Container $container)
  {
    return array(
      array(
        'hostname'  => 'ns.rainmaker.localdev.',
        'ipAddress' => '10.100.0.2',
      ),
      array(
        'hostname'  => 'ns',
        'ipAddress' => '10.100.0.2',
      )
    );
  }

  /**
   * @return array
   */
  public function getDnsRecordsForProjectContainer(Container $container)
  {
    $project = $this->getParentContainer($container);
    $records = array(
      array(
        'hostname'  => $project->shortHostname(),
        'ipAddress' => $project->getIPAddress(),
      )
    );

    $branches = $this->getProjectBranchContainers($project);
    usort($branches, array($this, 'cmpFqdnHostname'));
    foreach ($branches as $branch) {
      $hostname = $branch->shortHostname();
      if ($branch->getHostname() == $project->getDomain()) {
        $hostname = '@';
      }
      $records[] = array(
        'hostname'  => $hostname,
        'ipAddress' => $branch->getIPAddress(),
      );
    }

    return $records;
  }

  /**
   * @return array
   */
  public function getDnsPtrRecordsForProjectContainer(Container $container)
  {
    $project = $this->getParentContainer($container);
    $explodedIp = explode('.', $project->reverseIPAddress());
    $records = array(
      array(
        'hostname'  => $project->getHostname() . '.',
        'ipAddress' => reset($explodedIp),
      )
    );

    $branches = $this->getProjectBranchContainers($project);
    usort($branches, array($this, 'cmpFqdnHostname'));
    foreach ($branches as $branch) {
      $explodedIp = explode('.', $branch->reverseIPAddress());
      $records[] = array(
        'hostname'  => $branch->getHostname() . '.',
        'ipAddress' => reset($explodedIp),
      );
    }

    return $records;
  }

  // Utility methods

  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

  public static function cmpFqdnHostname(Container $a, Container $b)
  {
    $aHostname = $a->reverseHostname();
    $bHostname = $b->reverseHostname();

    if ($aHostname == $bHostname) {
      return 0;
    }

    return $aHostname < $bHostname ? -1 : 1;
  }

  protected function getParentContainer(Container $container)
  {
    if (null !== ($id = $container->getParentId())) {
      return $this->findOneById($id);
    }

    return $container;
  }

}
