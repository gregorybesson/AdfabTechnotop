<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller\Frontend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     *
     */
    protected $options;
    /**
     * @var gameService
     */
    protected $gameService;

    /**
     * @var quizService
     */
    protected $quizService;

    /**
     * @var pageService
     */
    protected $pageService;

    /**
     * @var rewardService
     */
    protected $rewardService;

    /**
     * @var achievementService
     */
    protected $achievementService;

     /**
     * @var mailService
     */
    protected $mailService;

    /**
     * @var missionService
     */
    protected $missionService;

    /**
     * @var storyTellingService
     */
    protected $storyTellingService;

    public function indexAction()
    {

        $bitlyclient = $this->getOptions()->getBitlyUrl();
        $bitlyuser = $this->getOptions()->getBitlyUsername();
        $bitlykey = $this->getOptions()->getBitlyApiKey();

        $this->getViewHelper('HeadMeta')->setProperty('bt:client', $bitlyclient);
        $this->getViewHelper('HeadMeta')->setProperty('bt:user', $bitlyuser);
        $this->getViewHelper('HeadMeta')->setProperty('bt:key', $bitlykey);

        return new ViewModel();
    }

    public function contactAction()
    {
        $mailService = $this->getMailService();

        $to = '';
        $config = $this->getGameService()->getServiceManager()->get('config');
        if (isset($config['contact']['email'])) {
            $to = $config['contact']['email'];
        }

        $form = $this->getServiceLocator()->get('application_contact_form');
        $form->setAttribute('method', 'post');

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            $form->setData($data);
            if ($form->isValid()) {
                $from = $data['email'];
                $subject= 'Contact : '.$data['object'];
                $result = $mailService->createHtmlMessage($from, $to, $subject, 'application/email/question', array('data' => $data));

                if ($result) {
                   return $this->redirect()->toRoute('contact/contactconfirmation');
                }
            }
        }

        return new ViewModel(array(
                'form' => $form,
            )
        );
    }

    public function contactconfirmationAction()
    {
        return new ViewModel();
    }

    public function getMailService()
    {
        if (!$this->mailService) {
            $this->mailService = $this->getServiceLocator()->get('playgroundgame_message');
        }

        return $this->mailService;
    }

    public function setMailService($mailService)
    {
        $this->mailService = $mailService;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->getServiceLocator()->get('playgroundcore_module_options'));
        }

        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    protected function getViewHelper($helperName)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
    }
}
