<?php
namespace Application\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Math\Rand;

class IndexController extends AbstractActionController
{

    /**
     *
     * @var technoTopService
     */
    protected $technoTopService;

    public function technofeelAction()
    {
        $request = $this->getRequest();
        $service = $this->getTechnoTopService();
        $websites = array();

        $service->updateTechnoCategory();
        
        if (! $request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        if($request->getParam('num')){
            $num = $request->getParam('num');
        }else{
            $num = 50;
        }
        
        if($request->getParam('start')){
            $start = $request->getParam('start');
        }else{
            $start = 1;
        }

        if($request->getParam('url')){
            $url = $request->getParam('url');
            //$url='http://www.off.tv/';
            //$url='http://www.gemo.fr';

            $websites[$url] = $service->analyze($url);
            foreach($websites as $website => $technos){
                echo "Technos detectees site :" . $website . "\n";
                foreach($technos as $techno => $types){
                    echo $techno . "\n";
                    foreach($types as $type=>$entries){
                      echo "-- " . $type . "\n";
                    foreach($entries as $entry){
                      echo "---- " . $entry . "\n";
                    }
                    }
                }
            }
        }else{
            $websites = $service->batchAnalyze($num,'FR',$start);
        }

        /*foreach($websites as $website => $technos){
            echo "Technos detectees site :" . $website . "\n";
            foreach($technos as $techno => $types){
                echo $techno . "\n";
                //foreach($types as $type=>$entries){
                  //  echo "-- " . $type . "\n";
                    //foreach($entries as $entry){
                      //  echo "---- " . $entry . "\n";
                    //}
                //}
            }
        }*/

        return "\n ----------DONE---------- \n";
    }

    public function getTechnoTopService()
    {
        if (! $this->technoTopService) {
            $this->technoTopService = $this->getServiceLocator()->get('application_technotop_service');
        }

        return $this->technoTopService;
    }

    public function setTechnoTopService(TechnoTopService $technoTopService)
    {
        $this->technoTopService = $technoTopService;

        return $this;
    }
}