<?php

namespace Rainmaker\Entity;

use Doctrine\ORM\EntityRepository;

use Rainmaker\RainmakerException;

/**
 * Doctrine ORM EntityRepository for managing the Container entity
 */
class ContainerRepository extends EntityRepository
{

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

  public function containerExists($name) {
    return NULL !== $this->findOneByName($name);
  }

  public static function friendlyNameToContainerName($fname)
  {
    if (NULL === ($cname = preg_replace('/[^a-z0-9\.\-_]/', '-', substr(strtolower($fname), 0, 20)))) {
      throw new RainmakerException();
    }

    return $cname;
  }

}
