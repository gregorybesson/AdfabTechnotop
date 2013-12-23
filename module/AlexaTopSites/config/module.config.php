<?php
return array(
    'router' => array(
        'routes' => array(
            'frontend' => array(
                'child_routes' => array(
                    'alexa' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'alexa',
                            'defaults' => array(
                                'controller' => 'alexatopsitesconsole',
                                'action'     => 'topsites',
                            ),
                      		),
                        'may_terminate' => true,
                    ),
                ),
            ),

            'admin' => array(
                'child_routes' => array(
                    'applicationadmin' => array(
                        'type' => 'Literal',
                        'priority' => 1000,
                        'options' => array(
                            'route' => '/application-admin',
                            'defaults' => array(
                                'controller' => 'applicationadmin',
                                'action' => 'index',
                            ),
                        ),
                        'child_routes' =>array(
                            'statistics' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/statistics',
                                    'defaults' => array(
                                        'controller' => 'applicationadmin',
                                        'action' => 'index',
                                    ),
                                ),
                            ),
                            'download' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/download',
                                    'defaults' => array(
                                        'controller' => 'applicationadmin',
                                        'action'     => 'download',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'topsites' => array(
                    'options' => array(
                        'route'    => 'topsites <accessKeyId> <secretAccessKey> [--co=]',
                        'defaults' => array(
                            'controller' => 'alexatopsitesconsole',
                            'action'     => 'topsites'
                        )
                    )
                )
            )
        )
    ),

    'core_layout' => array(
        'alexatopsites' => array(
            'layout' => 'layout/2columns-right',
            'children_views' => array(
                'col_right'  => 'common/column-right.phtml',
            ),
        ),
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'     => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php'
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'alexatopsitesfront' => 'AlexaTopSites\Controller\Frontend\IndexController',
            'alexatopsitesadmin' => 'AlexaTopSites\Controller\Admin\IndexController',
            'alexatopsitesconsole' => 'AlexaTopSites\Controller\Console\IndexController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
            __DIR__ . '/../view/frontend',
        ),
    ),
);