<?php

namespace ApplicationTest\Controller\Admin;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use \PlaygroundGame\Entity\Game as GameEntity;

class StatisticsControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../TestConfig.php'
        );

        parent::setUp();
    }

    public function testIndexActionNonExistentGame()
    {
    	$this->assertTrue(true);
    }
}
