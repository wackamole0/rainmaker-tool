<?php

namespace Rainmaker\Tests;

/**
 * @package Rainmaker\Tests
 */
class AbstractUnitTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        error_reporting(E_ALL);
    }

    protected function getPathToTestAcceptanceFilesBaseDirectory()
    {
        return dirname(__FILE__) . '/../Resources/tests';
    }

}
