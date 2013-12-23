<?php
$settings = array(
	/**
	 * On peut déterminer un layout pour un module (la config doit se trouver dans global.php ou local.php)
	 * TODO : Descendre jusqu'à l'action
	 */
	/*'core_layout' => array(
			'AdfabUser' => 'layout/1column',
	),*/

    /**
     * Bitly Account : A service for shortening url :)
     */
    'bitlyUsername' => 'o_7t2s2bjmun',
    'bitlyApiKey'   => 'R_335290ffb3f5fc08b45d3e0e6678c3db',
    'bitlyUrl'      => 'http://api.bit.ly/v3/shorten',

    //Used to create the open graph stuff
    'facebookOpengraph' => array(
        'enable' => true,
        'appId'  => '118474821657382',
        'tags'   => array(
            'og:site_name'  => 'Site Playground',
            'og:type'       => 'game',
        ),
    ),

    /**
    * AdServing parameters
    */
    /*'adserving' => array(
        'cat1' => 'playground',
        'cat2' => '',
        'cat3' => ''
    ),*/

    /**
    * Social messages
    */
    'defaultShareMessage' => 'Venez jouez au jeu : __placeholder__ sur Playground',

    /**
     * Email transport
     *
     * Name of Zend Transport Class to use
     */
    'transport_class' => 'Zend\Mail\Transport\File',
    'options_class' => 'Zend\Mail\Transport\FileOptions',
    'options' => array(
	    'path' => 'data/mail/',
	),

	'ckeditor' => array(
        'BasePath' =>'lib/js/ckeditor',
        'Width'      => "100%",
        'Height'     => "340",
        'Language'   => 'fr',
        'Color'      => '#F7F7F7',
        /*
        'stylesSet'  => 'custom_styles:/lib/js/ckeditor-custom/ckeditor-styles.js',
        'templates_files'  => array('/lib/js/ckeditor-custom/ckeditor-templates.js'),
        'contentsCss'  => array('/lib/css/ckeditor-custom/ckeditor-css.css'),
        */

        // Full toolbars
        'Toolbar'    => array(
            array('Source','-','DocProps','Preview','Print'),
            array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'),
            array('Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt'),
            array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'),
            '/',
            array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
            array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl'),
            array('Link','Unlink','Anchor' ),
            array('Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'),
            '/',
            array('Styles','Format','Font','FontSize'),
            array('TextColor','BGColor' ),
            array('Maximize', 'ShowBlocks','-','About'),
        ),
        'ElFinderBaseURL'      => '/elfinder/ckeditor',
        'ElFinderWindowWidth'  => "1000",
        'ElFinderWindowHeight' => "650",
	),

	'QuConfig' => array(
        'QuElFinder'=>array(
            'QuRoots'=>array(
                'driver'        => 'LocalFileSystem',
                'path'          =>  dirname(dirname(__DIR__)).'/public/media',
                'URL'           =>  '/media/',
                'accessControl' => 'access'
            ),
            'BasePath'=>'/js/elfinder',
        ),
	),
	'googleAnalytics' => array(
        'id' => 'UA-32092452-1',

        /**
        * Tracking across multiple (sub)domains
    	* @see https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingSite
    	*/
        'domain_name' 	=> '',
        'allow_linker' => false,
	),
);

$zenddevelopertools = array(
    'profiler' => array(
        'enabled' => true,
        'strict' => false,
        'flush_early' => false,
        'cache_dir' => 'data/cache',
        'matcher' => array(),
        'collectors' => array(),
    ),
    'toolbar' => array(
        'enabled' => false,
        'auto_hide' => false,
        'position' => 'bottom',
        'version_check' => true,
        'entries' => array(),
        'template_hint' => true,
    ),
);

// 'Jhu\ZdtLoggerModule' config
// $sm->get('jhu.zdt_logger')->info('my log');
$zdt_logger = array(
    'logger' => 'Zend\Log\Logger'
);

/**
 * You do not need to edit below this line
 */
return array(
    'playgroundcore'         => $settings,
    'zenddevelopertools'=> $zenddevelopertools,
    'jhu'               => array('zdt_logger' => $zdt_logger)
);