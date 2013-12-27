<?php
namespace AlexaTopSites;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements ConsoleUsageProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            // Describe available commands
            'topsites <ACCESS_KEY_ID> <SECRET_ACCESS_KEY> [--co=] [--num=] [--start=]' => 'Get the num top websites from co COUNTRY_CODE',

            // Describe expected parameters
            array( 'ACCESS_KEY_ID', 'Your AWS Access Key Id' ),
            array( 'SECRET_ACCESS_KEY', 'Your AWS Secret Key' ),
            array( '--co',      '(optional) COUNTRY CODE for the search' ),
            array( '--num',     '(optional) Number of sites to grab. If none provided => num = 100' ),
            array( '--start',   '(optional) position of first site to grab. If none provided => start = 1' ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'alexatopsites_topsites_service' => 'AlexaTopSites\Service\Topsites',
            ),

            'factories' => array(

                'alexatopsites_alexatopsite_mapper' => function ($sm) {
                    $mapper = new \AlexaTopSites\Mapper\AlexaTopSite(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );

                    return $mapper;
                },
            ),
        );
    }
}