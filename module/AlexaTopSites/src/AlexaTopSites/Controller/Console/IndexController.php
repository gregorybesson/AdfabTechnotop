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
        
        $numFound = $service->getTopSites($accessKeyId, $secretAccessKey, $countryCode, $num);
        
        echo "FINISHED : " . $numFound . " elements found";
    }

    public function technofeelAction()
    {
        $request = $this->getRequest();
        
        if (! $request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }
        
        $reader = new \Zend\Config\Reader\Json();
        $data = $reader->fromFile(__DIR__ . '/../../../../config/apps.json');
        
        $call = new \Zend\Http\Request();
        $client = new \Zend\Http\Client();
        $client->getCookies();
        
        if($request->getParam('url')){
            $url = $request->getParam('url');
        }else{
            $url='http://www.off.tv/';
            //$url='http://www.gemo.fr';
        }

        $call->setUri($url);
        $response = $client->dispatch($call);
        
        //print_r($response->getCookies());
        
        if ($response->isSuccess()) {
            
            $regexScript = '/<script[^>]+src=("|\')([^"\']+)/i';
            preg_match_all($regexScript, $response->getBody(), $matches);
            
            $doc = new \DOMDocument();
            @$doc->loadHTML($response->getBody());
            
            $metas = $doc->getElementsByTagName('meta');
            $headers = $client->getResponse()->getHeaders()->toArray();
            
            print_r($headers);
            
            foreach ($data['apps'] as $appId => $app) {
                if (isset($app['script'])) {
                    $patterns = $this->parse($app['script']);
                    // print_r($patterns);
                    forEach ($patterns as $pattern) {
                        foreach ($matches[2] as $match) {
                            if (preg_match("/" . $pattern . "/i", $match)) {
            
                                echo $appId . " rec script : " . $pattern . "\n";
                            }
                        }
                    }
                }
                if (isset($app['html'])) {
                    $patterns = $this->parse($app['html']);
                    // print_r($patterns);
                    forEach ($patterns as $pattern) {
                        if (preg_match("/" . $pattern . "/i", $response->getBody())) {
                            echo $appId . " rec html : " . $pattern . "\n";
                        }
                    }
                }
                if (isset($app['meta'])) {
                    $patterns = $this->parse($app['meta']);
                    //print_r($patterns);
                    forEach ($patterns as $k=>$pattern) {
                        for ($i = 0; $i < $metas->length; $i ++) {
                            $meta = $metas->item($i);
                            if (strtolower($meta->getAttribute('name')) == strtolower($k)) {
                                $description = $meta->getAttribute('content');
                                if (preg_match("/" . $pattern . "/i", $description)) {
                                    echo $appId . " rec meta : " . $k. "-" .$pattern . "\n";
                                }
                            }
                        }
                    }
                }
                
                if (isset($app['headers'])) {
                    $patterns = $this->parse($app['headers']);
                    //print_r($patterns);
                    forEach ($patterns as $k=>$pattern) {
                        foreach ($headers as $title=>$value) {
                            if (strtolower($title) == strtolower($k)) {
                            
                                if (preg_match("/" . $pattern . "/i", $value)) {
                                 echo $appId . " rec headers : " . $k. "-" .$pattern . "\n";
                                }
                                //echo "PATTERN : " . $pattern . "TITLE :" . $title . "\n";

                            }
                        }
                    }
                }
            }

            
            //print_r($headers);
            echo "--FIN--";
        }
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
    
    protected function parse($patterns)
    {
        $attrs;
        $parsed = array();
    
        //echo "PARSE - PATTERNS : " . print_r($patterns) . "\n";
        // Convert single patterns to an array
        if( is_string($patterns)){
            $patterns = array($patterns);
        }
        foreach($patterns as $k=>$pattern){
            //echo "PARSE - PATTERN : " . $pattern . "\n";
            $attrs = array();
    
            $attributes = explode('\\;', $pattern);
            
            $i=0;
            foreach($attributes as $attr){
                //echo "ATTRIBUTE " .$i . "\n";
                if($i){
                    
                    /*print_r($attr);
                    echo "\n";*/
                    $newAttr = explode(':',$attr);
                    
                }else{
                    $attr = str_replace('/', '\/', $attr);
                    $attr = str_replace('\\\/', '\/', $attr);
                    /*echo "CLEF : " .$k . "\n";
                    print_r($attr);
                    echo "\n";*/
                    $parsed[$k] = $attr;
                }
                
                //echo "----------------- \n";
                $i++;
            }
        }
        return $parsed;
    }
}