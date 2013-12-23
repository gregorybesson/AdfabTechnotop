<?php
/**
 * ZfcUser Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = array(
    //'zend_db_adapter'             => 'Zend\Db\Adapter\Adapter',
    'user_entity_class'             => 'PlaygroundUser\Entity\User',
    'enable_registration'           => true,
    'enable_username'               => true,
    'enable_display_name'           => false,
    'auth_identity_fields'          => array( 'email' ),
    'login_form_timeout'            => 300,
    'user_form_timeout'             => 300,
    'login_after_registration'      => true,
    'use_registration_form_captcha' => false,

    /*'form_captcha_options' => array(
        'class'   => 'figlet',
        'options' => array(
            'wordLen'    => 5,
            'expiration' => 300,
            'timeout'    => 300,
        ),
    ),*/

    'use_redirect_parameter_if_present' => true,
    'user_login_widget_view_template'   => 'playground-user/header/login.phtml',
    'login_redirect_route'              => 'frontend',
    'logout_redirect_route'             => 'frontend',
    'password_cost'                     => 14,
    'enable_user_state'                 => true,
    'default_user_state'                => 1,
    'allowed_login_states'              => array( 1 , 2 ),

    /**
     * End of ZfcUser configuration
     */
);

/**
 * You do not need to edit below this line
 */
return array(
    'zfcuser' => $settings,
    'service_manager' => array(
        'aliases' => array(
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ),
    ),
);
