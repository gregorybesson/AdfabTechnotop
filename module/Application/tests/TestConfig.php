<?php
return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineDataFixtureModule',
        'ZendDeveloperTools',
        'Jhu\ZdtLoggerModule',
        'PlaygroundTemplateHint',
        'AsseticBundle',
        'ZfcBase',
        'ZfcUser',
        'BjyAuthorize',
        'PlaygroundCore',
        'PlaygroundDesign',
        'PlaygroundFaq',
        'PlaygroundUser',
        'PlaygroundFacebook',
        'PlaygroundTranslate',
        'Application',
        'AlexaTopSites'
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,local,testing}.php',
        	'./config/{,*.}{testing}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
);
