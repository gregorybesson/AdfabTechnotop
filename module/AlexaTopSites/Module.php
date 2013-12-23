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
            'topsites <ACCESS_KEY_ID> <SECRET_ACCESS_KEY> [--co=]' => 'Get the 10 top websites from co COUNTRY_CODE',

            // Describe expected parameters
            array( 'ACCESS_KEY_ID', 'Your AWS Access Key Id' ),
            array( 'SECRET_ACCESS_KEY', 'Your AWS Secret Key' ),
            array( '--co',     '(optional) COUNTRY CODE for the search' ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'alexatopsites_topsites_service' => 'AlexaTopSites\Service\TopSites',
            ),
        );
    }
}