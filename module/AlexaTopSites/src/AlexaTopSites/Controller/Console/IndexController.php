<?php
namespace AlexaTopSites\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Math\Rand;

class IndexController extends AbstractActionController
{

    /**
     *
     * @var topSitesService
     */
    protected $topSitesService;

    public function topsitesAction()
    {
        $service = $this->getTopSitesService();
        $request = $this->getRequest();

        if (! $request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $accessKeyId = $request->getParam('accessKeyId');
        $secretAccessKey = $request->getParam('secretAccessKey');
        $countryCode = $request->getParam('co');
        if ($request->getParam('num')) {
            $num = $request->getParam('num');
        } else {
            $num = 100;
        }
        
        if ($request->getParam('start')) {
            $start = $request->getParam('start');
        } else {
            $start = 1;
        }

        $numFound = $service->getTopSites($accessKeyId, $secretAccessKey, $countryCode, $num, $start);

        echo "FINISHED : " . $numFound . " elements found";
    }

    public function getTopSitesService()
    {
        if (! $this->topSitesService) {
            $this->topSitesService = $this->getServiceLocator()->get('alexatopsites_topsites_service');
        }

        return $this->topSitesService;
    }

    public function setTopSitesService(TopSitesService $topSitesService)
    {
        $this->topSitesService = $topSitesService;

        return $this;
    }
}