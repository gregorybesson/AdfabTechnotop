<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'application_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/Application/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'application_entity'
                )
            )
        )
    ),
    'router' => array(
        'routes' => array(
            // this config must be out of frontend config...
            'techno' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/techno[/:id]',
                    /*'constraints' => array(
                        'id' => '[a-zA-Z0-9_-]+',
                    ),*/
                    'defaults' => array(
                        'controller' => 'Application\Controller\Rest\Techno'
                    ),
                ),
            ),
            'category' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/category[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Rest\Category'
                    ),
                ),
            ),
            'frontend' => array(
                'options' => array(
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
                'child_routes' => array(
                    'contact' => array(
                    	'type' => 'Literal',
                    	'options' => array(
                    		'route' => 'contactez-nous',
                    		'defaults' => array(
                    			'controller' => 'Application\Controller\Index',
                    			'action'     => 'contact',
               				),
                  		),
                    	'may_terminate' => true,
                    	'child_routes' => array(
               				'contactconfirmation' => array(
                    			'type'    => 'Literal',
                    			'options' => array(
                    				'route'    => '/confirmation',
                    				'defaults' => array(
                    					'controller' => 'Application\Controller\Index',
                    					'action'     => 'contactconfirmation',
                    				),
                    			),
                    		),
                    	),
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
                'technofeel' => array(
                    'options' => array(
                        'route'    => 'technofeel [--url=] [--num=] [--start=] [--techno=]',
                        'defaults' => array(
                            'controller' => 'technotopconsole',
                            'action'     => 'technofeel'
                        )
                    )
                )
            )
        )
    ),

    'core_layout' => array(
        'application' => array(
            'layout' => 'layout/homepage-2columns-right',
            'children_views' => array(
                'col_right'  => 'common/column-right.phtml',
            ),
            'controllers' => array(
                'Application\Controller\Index'   => array(
                    'layout' => 'layout/1column',
                    'actions' => array(
                        'index' => array(
                            'layout' => 'layout/homepage-2columns-right',
                            'children_views' => array(
                                'col_right'  => 'application/common/column_right.phtml',
                            ),
                        ),
                        'jeuxconcours' => array(
                            'layout' => 'layout/jeuxconcours-2columns-right',
                            'children_views' => array(
                                'col_right'  => 'application/common/column_right.phtml',
                            ),
                        ),
                        'prizecategories' => array(
                            'layout' => 'layout/2columns-right',
                            'children_views' => array(
                                'col_right'  => 'application/common/column_right.phtml',
                            ),
                        ),
                        'badges' => array(
                            'layout' => 'layout/2columns-left',
                            'children_views' => array(
                                'col_left'  => 'playground-user/user/col-user.phtml',
                            ),
                        ),
                        'sponsorfriends' => array(
                            'layout' => 'layout/2columns-left',
                            'children_views' => array(
                                'col_left'  => 'playground-user/user/col-user.phtml',
                            ),
                        ),
                        'contact' => array(
                            'layout' => 'layout/2columns-left',
                            'children_views' => array(
                                'col_left'  => 'playground-user/user/col-user.phtml',
                            ),
                        ),
                        'contactconfirmation' => array(
                            'layout' => 'layout/2columns-left',
                            'children_views' => array(
                                'col_left'  => 'playground-user/user/col-user.phtml',
                            ),
                        ),
                    ),
                ),
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
            'Application\Controller\Index' => 'Application\Controller\Frontend\IndexController',
            'Application\Controller\Rest\Techno' => 'Application\Controller\Rest\TechnoController',
            'Application\Controller\Rest\Category' => 'Application\Controller\Rest\CategoryController',
            'applicationadmin' => 'Application\Controller\Admin\StatisticsController',
            'technotopconsole' => 'Application\Controller\Console\IndexController',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
            __DIR__ . '/../view/frontend',
        ),
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Comment ça marche ?',
                'route' => 'commentcamarche',
            ),
            array(
                'label' => 'Mes badges et mes points',
                'route' => 'badges',
            ),
            array(
                'label' => 'Parrainer mes amis',
                'route' => 'sponsorfriends',
            ),
            array(
                'label' => 'Jeux concours',
                'route' => 'jeuxconcours',
            ),
            'pagination' => array(
                'label' => 'Jeux concours',
                'route'=> '/:p',
                'controller' => 'Application\Controller\Index',
                'action'     => 'jeuxconcours',
            ),
            array(
                'label' => 'Contactez-nous',
                'route' => 'contact',
            ),
            array(
                'label' => 'Contactez-nous',
                'route' => 'confirmation',
                'controller' => 'Application\Controller\Index',
                'action'     => 'contactconfirmation',
            ),
            array(
                'label' => 'Thématiques',
                'route' => 'thematiques[/:prizecategoryname][/:prizecategory]',
                'controller' => 'Application\Controller\Index',
                'action'     => 'prizecategories',
            ),
        ),
        /*'admin' => array(
            'applicationadmin' => array(
                'label' => 'Statistiques',
                'route' => 'admin/applicationadmin/statistics',
                'resource' => 'application',
                'privilege' => 'list',
            ),
        ),*/
    ),
);
