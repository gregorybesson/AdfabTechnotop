<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {

        $sm = $e->getApplication()->getServiceManager();

        $options = $sm->get('playgroundcore_module_options');
        $locale = $options->getLocale();
        $translator = $sm->get('translator');
        if (!empty($locale)) {
            //translator
            $translator->setLocale($locale);

            // plugins
            $translate = $sm->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);
        }
        AbstractValidator::setDefaultTranslator($translator,'playgroundcore');

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(

                'application_contact_form' => function($sm) {
                    $translator = $sm->get('translator');
                      $form = new Form\Contact(null, $sm, $translator);

                    return $form;
                },
            ),
        );
    }
}
