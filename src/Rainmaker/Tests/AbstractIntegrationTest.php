<?php

namespace Rainmaker\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Rainmaker\Tests
 */
class AbstractIntegrationTest extends WebTestCase
{

    protected $em;
    protected $contRepo;

    protected function setUp()
    {
        error_reporting(E_ALL);
        static::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->contRepo = $this->em->getRepository('Rainmaker:Container');
    }

    protected function getPathToTestAcceptanceFilesBaseDirectory()
    {
        return dirname(__FILE__) . '/../Resources/tests';
    }

}
