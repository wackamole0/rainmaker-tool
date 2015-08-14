<?php

namespace Rainmaker\Entity;

use Doctrine\ORM\EntityRepository;

use Rainmaker\RainmakerException;

/**
 * Doctrine ORM EntityRepository for managing the Container entity.
 */
class ContainerRepository extends EntityRepository
{
  protected $defaultNetworkPrefix             = '10.100';
  protected $defaultNetworkMin                = 1;
  protected $defaultNetworkMax                = 255;
  protected $defaultNetworkHostAddressMin     = 1;
  protected $defaultNetworkHostAddressMax     = 254;

  /**
   * Creates a new instance of the Container entity and optional saves to the database.
   *
   * @param $name
   * @param string $friendlyName
   * @param bool|FALSE $persist
   * @return Container
   */
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

  /**
   * Saves a container to the database.
   *
   * @param Container $container
   */
  public function saveContainer(Container $container)
  {
    $this->getEntityManager()->persist($container);
    $this->getEntityManager()->flush();
  }

  /**
   * Checks to see if a container with the given name already exists in the database.
   *
   * @param $name
   * @return bool
   */
  public function containerExists($name)
  {
    return NULL !== $this->findOneByName($name);
  }

  /**
   * Checks to see if a project container with the given name already exists in the database.
   *
   * @param $name
   * @return bool
   */
  public function projectContainerExists($name)
  {
    return NULL !== $this->createQueryBuilder('c')
      ->where('c.parentId IS NULL AND c.name = :name')
      ->setParameter('name', $name)
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->setMaxResults(1)
      ->getOneOrNullResult();
  }

  /**
   * Checks to see if a project branch container with the given name already exists in the database.
   *
   * @param $name
   * @return bool
   */
  public function projectBranchContainerExists($name)
  {
    return NULL !== $this->createQueryBuilder('c')
      ->where('c.parentId IS NOT NULL AND c.name = :name')
      ->setParameter('name', $name)
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->setMaxResults(1)
      ->getOneOrNullResult();
  }

  /**
   * Returns an array of all the project containers. This is deprecated and getProjectParentContainers
   * is preferred.
   *
   * @return Container[]
   * @deprecated
   */
  public function getAllParentContainers() {
    return $this->createQueryBuilder('c')
      ->where('c.parentId IS NULL')
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  /**
   * Returns an array of all the project containers.
   *
   * @return Container[]
   */
  public function getProjectParentContainers()
  {
    //@todo Can we do away with this alias?
    return $this->getAllParentContainers();
  }

  /**
   * Returns an array of all the branch containers for the given project container.
   *
   * @param Container $container
   * @return Container[]
   */
  public function getProjectBranchContainers(Container $container)
  {
    return $this->createQueryBuilder('c')
      ->where('c.parentId = :parentId')
      ->setParameter('parentId', $container->getId())
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  /**
   * Returns an array of all the branch containers for all projects.
   *
   * @return Container[]
   */
  public function getAllProjectBranchContainers()
  {
    return $this->createQueryBuilder('c')
      ->where('c.parentId IS NOT NULL')
      ->orderBy('c.parentId', 'ASC')
      ->addOrderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  // Network and IP address methods

  /**
   * Returns an array containing all of the unused Rainmaker (sub)network prefixes.
   *
   * @return string[]
   */
  public function getAvailableNetworks()
  {
    return array_diff($this->getAllNetworks(), $this->getAllNetworksInUse());
  }

  /**
   * Returns an array containing all possible Rainmaker (sub)network prefixes.
   *
   * @return array
   */
  public function getAllNetworks()
  {
    $networks = array();
    for($i = $this->defaultNetworkMin; $i <= $this->defaultNetworkMax; $i++) {
      $networks[] = $this->defaultNetworkPrefix . '.' . $i . '.0';
    }
    return $networks;
  }

  /**
   * Returns an array containing all of the Rainmaker (sub)network prefixes that are in use.
   *
   * @return string[]
   */
  public function getAllNetworksInUse()
  {
    $networks = array();
    foreach ($this->getProjectParentContainers() as $projectContainer) {
      $networks[] = $projectContainer->getNetworkAddress();
    }
    sort($networks);
    return $networks;
  }

  /**
   * Returns the next available Rainmaker (sub)network prefix or null if none are left.
   *
   * @return string|null
   */
  public function getNextAvailableNetwork()
  {
    $availableNetworks = $this->getAvailableNetworks();
    return reset($availableNetworks);
  }

  /**
   * Returns an array of all the host IP addresses available on the Rainmaker (sub)network that is
   * reserved for the given project container.
   *
   * @param Container $container
   * @return string[]
   */
  public function getAvailableNetworkHostAddresses(Container $container)
  {
    return array_diff($this->getAllNetworkHostAddresses($container), $this->getAllNetworkHostAddressesInUse($container));
  }

  /**
   * Returns an array of all the possible host IP address on the Rainmaker (sub)network that is
   * reserved for the given project container.
   *
   * @param Container $container
   * @return string[]
   */
  public function getAllNetworkHostAddresses(Container $container)
  {
    $networkPrefix = $container->networkPrefix();
    $networkHostAddresses = array();
    for($i = $this->defaultNetworkHostAddressMin; $i <= $this->defaultNetworkHostAddressMax; $i++) {
      $networkHostAddresses[] = $networkPrefix . '.' . $i;
    }
    return $networkHostAddresses;
  }

  /**
   * Returns an array of all the host IP address on the Rainmaker (sub)network that is
   * reserved for the given project container that are in use.
   *
   * @param Container $container
   * @return string[]
   */
  public function getAllNetworkHostAddressesInUse(Container $container)
  {
    $project = $this->getParentContainer($container);
    $addresses = array($project->getIPAddress());
    foreach ($this->getProjectBranchContainers($container) as $projectBranchContainer) {
      $addresses[] = $projectBranchContainer->getIPAddress();
    }
    sort($addresses);
    return $addresses;
  }

  /**
   * Returns the next available host IP address on the Rainmaker (sub)network that is
   * reserved for the given project container.
   *
   * @param Container $container
   * @return string[]
   */
  public function getNextAvailableNetworkHostAddress(Container $container)
  {
    $availableIps = $this->getAvailableNetworkHostAddresses($container);
    return reset($availableIps);
  }

  /**
   * Returns the first usable host IP address in the range of (sub)network host IP addresses
   * reserved for the given project container.
   *
   * @return string
   */
  public function getNetworkHostAddrRangeMin(Container $container)
  {
    $project = $this->getParentContainer($container);
    return $project->networkPrefix() . '.' . $this->defaultNetworkHostAddressMin;
  }

  /**
   * Returns the last usable host IP address in the range of (sub)network host IP addresses
   * reserved for the given project container.
   *
   * @return string
   */
  public function getNetworkHostAddrRangeMax(Container $container)
  {
    $project = $this->getParentContainer($container);
    return $project->networkPrefix() . '.' . $this->defaultNetworkHostAddressMax;
  }

  // DHCP methods

  /**
   * Returns an array of containers ordered for passing to the DHCP configuration files
   * templating system.
   *
   * @return Container[]
   */
  public function getAllContainersOrderedForHostsInclude() {
    return $this->createQueryBuilder('c')
      ->orderBy('c.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  // DNS methods

  /**
   * Returns an array of primary name servers which can resolve the .localdev subdomain in use
   * by the given container.
   *
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
   * Returns and array of name server host to IP mappings. Each element of the returned array is
   * an array containing a "hostname" key mapping to the hostname of the name server, and
   * an "ipAddress" key mapping to the IP address of the name server.
   *
   * @return array[]
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
   * Returns an array of DNS A records for all the hosts inside the Rainmaker (sub)network in use by
   * the given container.
   *
   * @return array[]
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
        $hostname = $branch->getHostname() . '.';
      }
      $records[] = array(
        'hostname'  => $hostname,
        'ipAddress' => $branch->getIPAddress(),
      );
    }

    return $records;
  }

  /**
   * Returns an array of DNS PTR records for all the hosts inside the Rainmaker (sub)network in use by
   * the given container.
   *
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

  // Fstab methods

  /**
   * Returns an array of containers requiring Rainmaker LXC cache fstab entries.
   *
   * @return Container[]
   */
  public function getAllContainersOrderedForFstabToolMounts()
  {
    return $this->getProjectParentContainers();
  }

  /**
   * Returns an array of containers requiring export via the NFS service.
   *
   * @return Container[]
   */
  public function getAllContainersOrderedForFstabNfsMounts()
  {
    return $this->getAllProjectBranchContainers();
  }

  /**
   * Returns an array of all the mount points which mount the Rainmaker LXC cache.
   *
   * @return array
   */
  public function getAllFstabToolsMountPoint()
  {
    $mounts = array();
    foreach ($this->getAllContainersOrderedForFstabToolMounts() as $container) {
      $mounts[] = $this->getFstabToolsMountPointForContainer($container);
    }
    return $mounts;
  }

  /**
   * Returns an array of all the mount points for the NFS exports.
   *
   * @return array
   */
  public function getAllFstabNfsMountPoint()
  {
    $mounts = array();
    foreach ($this->getAllContainersOrderedForFstabNfsMounts() as $container) {
      $mounts[] = $this->getFstabNfsMountPointForContainer($container);
    }
    return $mounts;
  }

  /**
   * Returns an array mapping the source and target filesystem locations for the given container's
   * Rainmaker LXC cache.
   *
   * @param Container $container
   * @return array|null
   */
  public function getFstabToolsMountPointForContainer(Container $container)
  {
    if ($container->isProjectBranch()) {
      return null;
    }

    return array(
      'source' => '/var/cache/lxc/rainmaker',
      'target' => $container->getLxcRootFs() . '/var/cache/lxc/rainmaker'
    );
  }

  /**
   * Returns an array mapping the source and target filesystem locations for the given container's
   * NFS export.
   *
   * @param Container $container
   * @return array|null
   */
  public function getFstabNfsMountPointForContainer(Container $container)
  {
    if ($container->isProject()) {
      return null;
    }

    $project = $this->getParentContainer($container);
    return array(
      'source' => $project->getLxcRootFs() . '/var/lib/lxc/' .  $container->getName() . '/rootfs/var/www/html',
      'target' => '/export/rainmaker/' . $container->getName()
    );
  }

  // Utility methods

  /**
   * Takes a string and returns a name which can be used as a unique identifier.
   *
   * @param $fname
   * @return mixed
   * @throws RainmakerException
   */
  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

  /**
   * Compares the host names of each of the supplied containers.
   *
   * @param Container $a
   * @param Container $b
   * @return int
   */
  public static function cmpFqdnHostname(Container $a, Container $b)
  {
    $aHostname = $a->reverseHostname();
    $bHostname = $b->reverseHostname();

    if ($aHostname == $bHostname) {
      return 0;
    }

    return $aHostname < $bHostname ? -1 : 1;
  }

  /**
   * Returns the (parent) project container for the given container. If the container is a project
   * branch container then its project container will be returned. If the container is a project
   * container then it will be returned.
   *
   * @param Container $container
   * @return Container
   */
  public function getParentContainer(Container $container)
  {
    if (null !== ($id = $container->getParentId())) {
      return $this->findOneById($id);
    }

    return $container;
  }

}
