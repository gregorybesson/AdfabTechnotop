<?php
return array(
	'assetic_configuration' => array(
		'modules' => array(
			'default_base' => array(
				'root_path' => array(
					__DIR__ . '/../../../../design/frontend/default/base/assets',
				),
                # collection of assets
                'collections' => array(
                    
                    'head_frontend_lib' => array(
                        'assets' => array(
                            'js/lib/jquery-1.9.0.min.js',
                            'js/lib/bootstrap.min.js',
                            'js/lib/bootstrap-datepicker.js',
                            'js/lib/jquery.validate.min.js',
                            'js/lib/wScratchPad.js',
                            'js/lib/jquery.timer.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/js/head_lib.js',
                        ),
                    ),
                    
                    'head_frontend_js' => array(
                        'assets' => array(
                            'loader.js'      => 'js/loader.js',
                            'popin.js'       => 'js/popin.js',
                            'functions.js'   => 'js/functions.js',
                            'script.js'      => 'js/script.js',
                            'users.js'       => 'js/users.js',
                            'share.js'       => 'js/share.js',
                            'games.js'       => 'js/games.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/js/head_main.js',
                        ),
                    ),
                    
                    'frontend_images' => array(
                        'assets' => array(
                            'images/**/*.png',
                            'images/**/*.jpg',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'frontend',
                        )
                    ),

                    'frontend_fonts' => array(
                        'assets' => array(
                            'fonts/**/*.eot',
                            'fonts/**/*.svg',
                            'fonts/**/*.ttf',
                            'fonts/**/*.woff',
                            'fonts/*.eot',
                            'fonts/*.svg',
                            'fonts/*.ttf',
                            'fonts/*.woff',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'frontend'
                        )
                    ),
                    
                    'frontend_css' => array(
                        'assets' => array(
                            'css/base.css'
                        ),
                        'options' => array(
                            'output' => 'frontend/css/base'
                        )
                    ),
                ),
			),
		),
        'routes' => array( 
            'frontend.*' => array(
                '@frontend_lib_css'     => '@frontend_lib_css',
                '@frontend_css'         => '@frontend_css',
                '@head_frontend_lib'    => '@head_frontend_lib',
                '@head_frontend_js'     => '@head_frontend_js',
            ),
        ),
	),
);