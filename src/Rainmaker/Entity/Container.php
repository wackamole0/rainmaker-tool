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

  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

}
