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

  const STATE_OFFLINE           =  0;
  const STATE_CREATING_LXC      =  1;
  const STATE_STARTING_LXC      =  2;
  const STATE_PROVISIONING_LXC  =  3;
  const STATE_ONLINE            = 10;

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
   * @ORM\Column(type="string", length=41)
   */
  protected $lxcUtsName;

  /**
   * @ORM\Column(type="string", length=17)
   */
  protected $lxcHwAddr;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $lxcRootFs;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $hostname;

  /**
   * @ORM\Column(type="string", length=15)
   */
  protected $networkAddress;

  /**
   * @ORM\Column(type="string", length=15)
   */
  protected $ipAddress;

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

  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

  public function reverseHostname() {
    return implode('.', array_reverse(explode('.', $this->getHostname())));
  }

  public function getIpAddrRangeMin() {
    return '10.100.1.1';
  }

  public function getIpAddrRangeMax() {
    return '10.100.1.254';
  }

}
