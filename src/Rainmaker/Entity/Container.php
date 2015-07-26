<?php

namespace Rainmaker\Entity;

use Doctrine\ORM\Mapping as ORM;

use Rainmaker\RainmakerException;

/**
 * @ORM\Entity(repositoryClass="Rainmaker\Entity\ContainerRepository")
 * @ORM\Table(name="container")
 */
class Container
{

  const STATE_PENDING_PROVISIONING  =  0;
  const STATE_PROVISIONING          =  1;
  const STATE_STOPPED               =  2;
  const STATE_STARTING              =  3;
  const STATE_RUNNING               =  4;
  const STATE_STOPPING              =  5;
  const STATE_DESTROYING            =  6;

  const STATE_ERROR                 = -1;

  protected static $statuses = array(
    self::STATE_PENDING_PROVISIONING  => 'Pending Prov.',
    self::STATE_PROVISIONING          => 'Provisioning',
    self::STATE_STOPPED               => 'Stopped',
    self::STATE_STARTING              => 'Starting',
    self::STATE_RUNNING               => 'Running',
    self::STATE_STOPPING              => 'Stopping',
    self::STATE_DESTROYING            => 'Destroying',
    self::STATE_ERROR                 => 'Error'
  );

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $parentId;

  /**
   * @ORM\Column(type="string", length=41, unique=TRUE)
   */
  protected $name;

  /**
   * @ORM\Column(type="string", length=41)
   */
  protected $friendlyName;

  /**
   * @ORM\Column(type="string", length=41, nullable=TRUE)
   */
  protected $lxcUtsName;

  /**
   * @ORM\Column(type="string", length=17, nullable=TRUE)
   */
  protected $lxcHwAddr;

  /**
   * @ORM\Column(type="string", length=255, nullable=TRUE)
   */
  protected $lxcRootFs;

  /**
   * @ORM\Column(type="string", length=255, nullable=TRUE)
   */
  protected $hostname;

  /**
   * @ORM\Column(type="string", length=255, nullable=TRUE)
   */
  protected $domain;

  /**
   * @ORM\Column(type="string", length=15, nullable=TRUE)
   */
  protected $networkAddress;

  /**
   * @ORM\Column(type="string", length=15, nullable=TRUE)
   */
  protected $ipAddress;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $dnsZoneTtl;

  /**
   * @ORM\Column(type="string", length=255, nullable=TRUE)
   */
  protected $dnsZonePriMasterNs;

  /**
   * @ORM\Column(type="string", length=255, nullable=TRUE)
   */
  protected $dnsZoneAdminEmail;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $dnsZoneSerial;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $dnsZoneRefresh;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $dnsZoneRetry;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $dnsZoneExpire;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $dnsZoneNegCacheTtl;

  /**
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $state = 0;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
      return $this->id;
  }

  /**
   * Set parentId
   *
   * @param integer $parentId
   * @return Container
   */
  public function setParentId($parentId)
  {
    $this->parentId = $parentId;

    return $this;
  }

  /**
   * Get parentId
   *
   * @return integer
   */
  public function getParentId()
  {
    return $this->parentId;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return Container
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set friendlyName
   *
   * @param string $friendlyName
   * @return Container
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;

    return $this;
  }

  /**
   * Get friendlyName
   *
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }

  /**
   * Get LXC UTS name
   *
   * @return string
   */
  public function getLxcUtsName()
  {
    return $this->name;
  }

  /**
   * Set LXC UTS name
   *
   * @param string $lxcUtsName
   * @return Container
   */
  public function setLxcUtsName($lxcUtsName)
  {
    $this->lxcUtsName = $lxcUtsName;

    return $this;
  }

  /**
   * Get LXC Hardware Addr
   *
   * @return string
   */
  public function getLxcHwAddr()
  {
    return $this->lxcHwAddr;
  }

  /**
   * Set LXC Hardware Addr
   *
   * @param string $lxcHwAddr
   * @return Container
   */
  public function setLxcHwAddr($lxcHwAddr)
  {
    $this->lxcHwAddr = $lxcHwAddr;

    return $this;
  }

  /**
   * Get LXC Root FS
   *
   * @return string
   */
  public function getLxcRootFs()
  {
    return $this->lxcRootFs;
  }

  /**
   * Set LXC Root FS
   *
   * @param string $lxcRootFs
   * @return Container
   */
  public function setLxcRootFs($lxcRootFs)
  {
    $this->lxcRootFs = $lxcRootFs;

    return $this;
  }

  /**
   * Get Hostname
   *
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }

  /**
   * Set Hostname
   *
   * @param string $hostname
   * @return Container
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;

    return $this;
  }

  /**
   * Get Domain Name
   *
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }

  /**
   * Set Domain Name
   *
   * @param string $domain
   * @return Container
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;

    return $this;
  }

  /**
   * Get Network Address
   *
   * @return string
   */
  public function getNetworkAddress()
  {
    return $this->networkAddress;
  }

  /**
   * Set Network Address
   *
   * @param string $networkAddress
   * @return Container
   */
  public function setNetworkAddress($networkAddress)
  {
    $this->networkAddress = $networkAddress;

    return $this;
  }

  /**
   * Get IP Address
   *
   * @return string
   */
  public function getIPAddress()
  {
    return $this->ipAddress;
  }

  /**
   * Set IP Address
   *
   * @param string $ipAddress
   * @return Container
   */
  public function setIPAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;

    return $this;
  }

  /**
   * Get DNS Zone TTL
   *
   * @return string
   */
  public function getDnsZoneTtl()
  {
    return $this->dnsZoneTtl;
  }

  /**
   * Set DNS Zone TTL
   *
   * @param string $dnsZoneTtl
   * @return Container
   */
  public function setDnsZoneTtl($dnsZoneTtl)
  {
    $this->dnsZoneTtl = $dnsZoneTtl;

    return $this;
  }

  /**
   * Get DNS zone primary master name server
   *
   * @return string
   */
  public function getDnsZonePriMasterNs()
  {
    return $this->dnsZonePriMasterNs;
  }

  /**
   * Set DNS zone primary master name server
   *
   * @param string $dnsZonePriMasterNs
   * @return Container
   */
  public function setDnsZonePriMasterNs($dnsZonePriMasterNs)
  {
    $this->dnsZonePriMasterNs = $dnsZonePriMasterNs;

    return $this;
  }

  /**
   * Get DNS zone administrator email address
   *
   * @return string
   */
  public function getDnsZoneAdminEmail()
  {
    return $this->dnsZoneAdminEmail;
  }

  /**
   * Set DNS zone administrator email address
   *
   * @param string $dnsZoneAdminEmail
   * @return Container
   */
  public function setDnsZoneAdminEmail($dnsZoneAdminEmail)
  {
    $this->dnsZoneAdminEmail = $dnsZoneAdminEmail;

    return $this;
  }

  /**
   * Get DNS zone serial
   *
   * @return string
   */
  public function getDnsZoneSerial()
  {
    return $this->dnsZoneSerial;
  }

  /**
   * Set DNS zone serial
   *
   * @param string $dnsZoneSerial
   * @return Container
   */
  public function setDnsZoneSerial($dnsZoneSerial)
  {
    $this->dnsZoneSerial = $dnsZoneSerial;

    return $this;
  }

  /**
   * Get DNS zone refresh
   *
   * @return string
   */
  public function getDnsZoneRefresh()
  {
    return $this->dnsZoneRefresh;
  }

  /**
   * Set DNS zone refresh
   *
   * @param string $dnsZoneRefresh
   * @return Container
   */
  public function setDnsZoneRefresh($dnsZoneRefresh)
  {
    $this->dnsZoneRefresh = $dnsZoneRefresh;

    return $this;
  }

  /**
   * Get DNS zone retry
   *
   * @return string
   */
  public function getDnsZoneRetry()
  {
    return $this->dnsZoneRetry;
  }

  /**
   * Set DNS zone retry
   *
   * @param string $dnsZoneRetry
   * @return Container
   */
  public function setDnsZoneRetry($dnsZoneRetry)
  {
    $this->dnsZoneRetry = $dnsZoneRetry;

    return $this;
  }

  /**
   * Get DNS zone expire
   *
   * @return string
   */
  public function getDnsZoneExpire()
  {
    return $this->dnsZoneExpire;
  }

  /**
   * Set DNS zone expire
   *
   * @param string $dnsZoneExpire
   * @return Container
   */
  public function setDnsZoneExpire($dnsZoneExpire)
  {
    $this->dnsZoneExpire = $dnsZoneExpire;

    return $this;
  }

  /**
   * Get DNS zone negative cache TTL
   *
   * @return string
   */
  public function getDnsZoneNegCacheTtl()
  {
    return $this->dnsZoneNegCacheTtl;
  }

  /**
   * Set DNS zone negative cache TTL
   *
   * @param string $dnsZoneNegCacheTtl
   * @return Container
   */
  public function setDnsZoneNegCacheTtl($dnsZoneNegCacheTtl)
  {
    $this->dnsZoneNegCacheTtl = $dnsZoneNegCacheTtl;

    return $this;
  }

  /**
   * Get the current numerical status of the container
   *
   * @return int
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Set the current numerical status of this container
   *
   * @param int $state
   * @return $this
   */
  public function setState($state)
  {
    $this->state = $state;
    return $this;
  }

  /**
   * Takes a string and returns a name which can be used as a unique identifier.
   *
   * @param $fname
   * @return mixed
   * @throws RainmakerException
   * @deprecated ContainerRepository::friendlyNameToContainerName($fname) is preferred to this method.
   */
  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

  /**
   * Returns the container's fully-qualified hostname and returns the host portion.
   *
   * @return string|null
   */
  public function shortHostname()
  {
    $explodedIp = explode('.', $this->getHostname());
    return reset($explodedIp);
  }

  /**
   * Returns the container's fully-qualified hostname reversed.
   *
   * @return string
   */
  public function reverseHostname()
  {
    return implode('.', array_reverse(explode('.', $this->getHostname())));
  }

  /**
   * Returns the container's domain name reversed.
   *
   * @return string
   */
  public function reverseDomain()
  {
    return implode('.', array_reverse(explode('.', $this->getDomain())));
  }

  /**
   * Returns the container's (sub)network prefix.
   *
   * @return null|string
   */
  public function networkPrefix()
  {
    $network = $this->getNetworkAddress();
    $networkPrefix = NULL;
    if (FALSE !== ($lastdot = strripos($network, '.'))) {
      $networkPrefix = substr($network, 0, $lastdot);
    }

    return $networkPrefix;
  }

  /**
   * Returns the container's (sub)network prefix in reverse.
   *
   * @return string
   */
  public function reverseNetworkPrefix()
  {
    return implode('.', array_reverse(explode('.', $this->networkPrefix())));
  }

  /**
   * Returns the reversal of this container's IP address.
   *
   * @return string
   */
  public function reverseIPAddress()
  {
    return implode('.', array_reverse(explode('.', $this->getIPAddress())));
  }

  public function getStatusText()
  {
    return static::$statuses[$this->getState()];
  }

  /**
   * Returns an array mapping this container's hostname and IP address for use as a DNS A record.
   *
   * @return array
   */
  public function getDnsRecord()
  {
    return array(
      'hostname'  => reset(explode('.', $this->getHostname())),
      'ipAddress' => $this->getIPAddress()
    );
  }

  /**
   * Returns an array mapping this container's hostname and IP address for use as a DNS PTR record.
   *
   * @return array
   */
  public function getDnsPtrRecord()
  {
    return array(
      'hostname'  => $this->getIPAddress() . '.',
      'ipAddress' => reset(explode('.', $this->reverseIPAddress()))
    );
  }

  /**
   * Returns an array mapping the source and target filesystem locations for this container's
   * Rainmaker LXC cache.
   *
   * @return array
   */
  public function getFstabToolsMountPoint()
  {
    return array(
      'source' => '/var/cache/lxc/rainmaker',
      'target' => $this->getLxcRootFs() . '/var/cache/lxc/rainmaker'
    );
  }

  /**
   * Returns an array mapping the source and target filesystem locations for this container's
   * NFS export.
   *
   * @return array
   */
  public function getFstabNfsMountPoint()
  {
    return array(
      'source' => $this->getLxcRootFs() . '/var/www/html',
      'target' => $this->getLxcRootFs() . '/export/rainmaker/' . $this->getName()
    );
  }

}
