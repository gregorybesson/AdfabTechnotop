<?php
return array(
    'core_layout' => array(
        'frontend' => array(
            'layout' => 'layout/2columns-right.phtml',
            'children_views' => array(
                'col_right' => 'application/common/column_right.phtml',
                'col_left' => 'playground-user/user/col-user.phtml'
            ),
            'channel' => array(
                'facebook' => array(
                    'layout' => 'layout/1column-facebook.phtml',
                ),
                'embed' => array(
                    'layout' => 'layout/1column-embed.phtml',
                ),
            ),
            'modules' => array(

                'playgroundfacebook' => array(
                    'layout' => 'layout/2columns-left',
                    'children_views' => array(
                        'col_left' => 'playground-user/user/col-user.phtml'
                    )
                ),

                'playgroundfaq' => array(
                    'layout' => 'layout/2columns-left',
                    'children_views' => array(
                        'col_left' => 'playground-user/user/col-user.phtml'
                    )
                ),

                'playgrounduser' => array(
                    'layout' => 'layout/2columns-left.phtml',
                    'controllers' => array(
                        'playgrounduser_user' => array(
                            'children_views' => array(
                                'col_left' => 'playground-user/user/col-user.phtml'
                            ),
                            'actions' => array(
                                'index' => array(
                                    'layout' => 'layout/1column.phtml'
                                ),
                                'register' => array(
                                    'layout' => 'layout/1column.phtml'
                                ),
                                'registermail' => array(
                                    'layout' => 'layout/1column.phtml'
                                ),
                                'address' => array(
                                    'layout' => 'layout/game-2columns-right.phtml'
                                )
                            )
                        ),
                        'playgrounduser_forgot' => array(
                            'layout' => 'layout/1column.phtml'
                        )
                    )
                ),

                'application' => array(
                    'controllers' => array(
                        'Application\Controller\Index' => array(
                            'actions' => array(
                                /*'index' => array(
                                    'layout' => 'layout/homepage-2columns-right.phtml',
                                ),*/
                                'contact' => array(
                                    'layout' => 'layout/2columns-left',
                                    'children_views' => array(
                                        'col_left' => 'playground-user/user/col-user.phtml'
                                    )
                                ),
                                'contactconfirmation' => array(
                                    'layout' => 'layout/2columns-left',
                                    'children_views' => array(
                                        'col_left' => 'playground-user/user/col-user.phtml'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    )
);