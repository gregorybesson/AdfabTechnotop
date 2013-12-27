<?php

return array(
    'bjyauthorize' => array(

        'default_role' => 'guest',
        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity',
        //'unauthorized_strategy' => 'PlaygroundUser\View\Strategy\UnauthorizedStrategy',
        'role_providers' => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array(),
                'user'  => array('children' => array(
                    'admin' => array(),
                )),
            ),

            'BjyAuthorize\Provider\Role\DoctrineEntity' => array(
                'role_entity_class' => 'PlaygroundUser\Entity\Role',
            ),
        ),

        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'game'          => array(),
                'user'          => array(),
                'core'          => array(),
                'reward'        => array(),
                'partner'       => array(),
                'cms'           => array(),
                'faq'           => array(),
                'facebook'      => array(),
                'application'   => array(),
                'flow'          => array(),
                'stats'         => array(),
                'design'        => array(),
                'translate'     => array(),
                'weather'       => array(),
                'emailcampaign' => array(),
            ),
        ),

        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin'), 'user',           array('list','add','edit','delete')),
                    array(array('admin'), 'faq',            array('list','add','edit','delete')),
                    array(array('admin'), 'facebook',       array('list','add','edit','delete')),
                    array(array('admin'), 'core',           array('dashboard', 'edit')),
                    array(array('admin'), 'application',    array('list')),
                    array(array('admin'), 'design',         array('system')),
                    array(array('admin'), 'translate',      array('list')),
                ),
            ),
        ),

        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                //Front Area
                array('controller' => 'index', 'action' => 'index',                'roles' => array('guest', 'user')),
                array('controller' => 'Application\Controller\Index',              'roles' => array('guest', 'user')),
                array('controller' => 'zfcuser',                                   'roles' => array('guest', 'user')),
                array('controller' => 'playgrounduser_user',                       'roles' => array('guest', 'user')),
                array('controller' => 'playgrounduser_forgot',                     'roles' => array('guest', 'user')),
                array('controller' => 'facebook',                                  'roles' => array('guest', 'user')),
                array('controller' => 'playgroundcore_console',                    'roles' => array('guest', 'user')),
                array('controller' => 'playgroundfaq',                             'roles' => array('guest', 'user')),
                array('controller' => 'Application\Controller\Rest\Techno',        'roles' => array('guest', 'user')),
                array('controller' => 'Application\Controller\Rest\Category',      'roles' => array('guest', 'user')),


                // Console
                array('controller' => 'AsseticBundle\Controller\Console',          'roles' => array('guest', 'user')),
                array('controller' => 'DoctrineModule\Controller\Cli',             'roles' => array('guest', 'user')),
                array('controller' => 'alexatopsitesconsole',                      'roles' => array('guest', 'user')),
                array('controller' => 'technotopconsole',                          'roles' => array('guest', 'user')),

                // Admin area
                //array('controller' => 'ZfcAdmin\Controller\AdminController',  'roles' => array('admin')),
                array('controller' => 'playgrounduseradmin_login',                 'roles' => array('guest', 'user')),
                array('controller' => 'playgrounduseradmin',                       'roles' => array('admin')),
                array('controller' => 'playgroundfaq_admin',                       'roles' => array('admin')),
                array('controller' => 'playgroundfacebook_admin_app',              'roles' => array('admin')),
                array('controller' => 'playgroundfacebook_admin_page',             'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\System',        'roles' => array('admin')),
                array('controller' => 'PlaygroundCore\Controller\Formgen',         'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\Dashboard',     'roles' => array('admin')),
                array('controller' => 'elfinder',                                  'roles' => array('admin')),
                array('controller' => 'DoctrineORMModule\Yuml\YumlController',     'roles' => array('admin')),
                array('controller' => 'applicationadmin',                          'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\CompanyAdmin',  'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\ThemeAdmin',    'roles' => array('admin')),
                array('controller' => 'PlaygroundTranslate\Controller\Admin\TranslateAdmin', 'roles' => array('admin')),
            ),
        ),
    ),
);