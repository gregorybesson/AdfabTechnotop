<?php
namespace AlexaTopSites\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Math\Rand;

class IndexController extends AbstractActionController
{

    /**
     * @var topSitesService
     */
    protected $topSitesService;

    public function topsitesAction()
    {

        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Get user email from console and check if the user used --verbose or -v flag
        $accessKeyId = $request->getParam('accessKeyId');
        $secretAccessKey   = $request->getParam('secretAccessKey');
        $countryCode     = $request->getParam('co');


        $service = $this->getTopSitesService();
        //$topSites = new TopSites($accessKeyId, $secretAccessKey, $countryCode);
        $service->getTopSites();

        /*if (!$verbose) {
            return "Done! $userEmail has received an email with his new password.\n";
        }else{
            return "Done! New password for user $userEmail is '$newPassword'. It has also been emailed to him. \n";
        }*/

        return $accessKeyId . '-' . $secretAccessKey . '-' . $countryCode;
    }

    public function getTopSitesService()
    {
        if (!$this->topSitesService) {
            $this->topSitesService = $this->getServiceLocator()->get('alexatopsites_topsites_service');
        }

        return $this->adminGameService;
    }

    public function setTopSitesService(TopSitesService $topSitesService)
    {
        $this->topSitesService = $topSitesService;

        return $this;
    }
}