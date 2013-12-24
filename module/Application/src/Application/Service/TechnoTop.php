<?php

namespace Application\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use Application\Entity\TechnoSite;

class TechnoTop  extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager;

    protected $em;
    protected $er;

    protected $pagination;
    protected $startNum;
    protected $countryCode;

    /**
     * Analyze the technos of a website
     */
    public function batchAnalyze($num=50,$country='FR') {
        $alexaTopSites = $this->getAlexaTopSites($num, $country);
        $websites = array();
        $i=0;
        foreach($alexaTopSites as $site) {
            if($i%100 == 0){
                foreach($websites as $website => $technos){
                    foreach($technos as $techno => $types){
                        $technoSite = $this->getTechnoSiteRepository()->findOneBy(array('url' => $website, 'techno' => $techno));
                        if (!$technoSite) {
                            $technoSite = new TechnoSite($website, $techno);
                        }
                        $this->getEntityManager()->persist($technoSite);
                    }
                }

                $this->getEntityManager()->flush();
                $this->getEntityManager()->clear();

                $websites = array();
                echo $i . " websites analyzed \n";
            }
            $websites[$site->getUrl()] = $this->analyze('http://'.$site->getUrl());
            $i++;
        }

        return true;
    }

    /**
     * Analyze the technos of a website
     */
    public function analyze($url) {

        $reader = new \Zend\Config\Reader\Json();
        $data = $reader->fromFile(__DIR__ . '/../../../config/apps.json');

        $technos = array();

        $call = new \Zend\Http\Request();
        $client = new \Zend\Http\Client();
        $client->getCookies();

        $call->setUri($url);
        $response = false;

        try{
            $response = $client->dispatch($call);
        } catch (\Exception $ex) {

        }

        //print_r($response->getCookies());

        if ($response && $response->isSuccess()) {

            $body='';
            try{
                $body = $response->getBody();
            } catch (\Exception $ex) {

            }

            $regexScript = '/<script[^>]+src=("|\')([^"\']+)/i';
            preg_match_all($regexScript, $body, $matches);

            $doc = new \DOMDocument();
            @$doc->loadHTML($body);

            $metas = $doc->getElementsByTagName('meta');
            $headers = $client->getResponse()->getHeaders()->toArray();

            foreach ($data['apps'] as $appId => $app) {

                if (isset($app['script'])) {
                    $patterns = $this->parse($app['script']);
                    // print_r($patterns);
                    forEach ($patterns as $pattern) {
                        foreach ($matches[2] as $match) {
                            if (preg_match("/" . $pattern . "/i", $match)) {
                                //echo $appId . " rec script : " . $pattern . "\n";
                                $technos[$appId]['script'][] = $pattern;
                                if (isset($app['implies'])) {
                                    $implies = $app['implies'];
                                    if(is_array($implies)){
                                        foreach($implies as $imply ){
                                            $technos[$imply]['implies'][] = '';
                                        }
                                    }elseif($implies){
                                        $technos[$implies]['implies'][] = '';
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($app['html'])) {
                    $patterns = $this->parse($app['html']);
                    // print_r($patterns);
                    forEach ($patterns as $pattern) {
                        if (preg_match("/" . $pattern . "/i", $body)) {
                            //echo $appId . " rec html : " . $pattern . "\n";
                            $technos[$appId]['html'][] = $pattern;
                            if (isset($app['implies'])) {
                                $implies = $app['implies'];
                                if(is_array($implies)){
                                    foreach($implies as $imply ){
                                        $technos[$imply]['implies'][] = '';
                                    }
                                }elseif($implies){
                                    $technos[$implies]['implies'][] = '';
                                }
                            }
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
                                    //echo $appId . " rec meta : " . $k. "-" .$pattern . "\n";
                                    $technos[$appId]['meta'][] = $k. " - " .$pattern;
                                    if (isset($app['implies'])) {
                                        $implies = $app['implies'];
                                        if(is_array($implies)){
                                            foreach($implies as $imply ){
                                                $technos[$imply]['implies'][] = '';
                                            }
                                        }elseif($implies){
                                            $technos[$implies]['implies'][] = '';
                                        }
                                    }
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
                            if(strtolower($title) != strtolower('Set-Cookie')){
                                if (strtolower($title) == strtolower($k)) {
                                    if (preg_match("/" . $pattern . "/i", $value)) {
                                        //echo $appId . " rec headers : " . $k. "-" .$pattern . "\n";
                                        $technos[$appId]['headers'][] = $k. " - " .$pattern;
                                        if (isset($app['implies'])) {
                                            $implies = $app['implies'];
                                            if(is_array($implies)){
                                                foreach($implies as $imply ){
                                                    $technos[$imply]['implies'][] = '';
                                                }
                                            }elseif($implies){
                                                $technos[$implies]['implies'][] = '';
                                            }
                                        }
                                    }
                                }
                            }elseif (strtolower($title) == strtolower($k)) {
                                foreach($value as $v){
                                    if (preg_match("/" . $pattern . "/i", $v)) {
                                        //echo $appId . " rec headers cookie : " . $k. "-" .$pattern . "\n";
                                        $technos[$appId]['cookie'][] = $k. " - " .$pattern;
                                        if (isset($app['implies'])) {
                                            $implies = $app['implies'];
                                            if(is_array($implies)){
                                                foreach($implies as $imply ){
                                                    $technos[$imply]['implies'][] = '';
                                                }
                                            }elseif($implies){
                                                $technos[$implies]['implies'][] = '';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $technos;
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

    /**
     *
     */
    public function getAlexaTopSites($maxResults = 50, $country='FR')
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('SELECT a FROM AlexaTopSites\Entity\AlexaTopSite a
                WHERE a.country = :country
                ORDER BY a.countryRank ASC');
        $query->setParameter('country', $country);
        $query->setMaxResults($maxResults);
        $sites = $query->getResult();

        return $sites;
    }

     /**
      * Retrieve service manager instance
      *
      * @return ServiceManager
      */
     public function getServiceManager()
     {
         return $this->serviceManager;
     }

     /**
      * Set service manager instance
      *
      * @param ServiceManager $serviceManager
      * @return Game
      */
     public function setServiceManager(ServiceManager $serviceManager)
     {
         $this->serviceManager = $serviceManager;

         return $this;
     }

     public function getEntityManager()
     {
         if (!$this->em) {
             $this->em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
         }

         return $this->em;
     }

     public function getEntityRepository()
     {
         if (!$this->er) {
             $this->er = $this->getEntityManager()->getRepository('AlexaTopSites\Entity\AlexaTopSite');
         }

         return $this->er;
     }

     public function getTechnoSiteRepository()
     {
         if (!$this->er) {
             $this->er = $this->getEntityManager()->getRepository('Application\Entity\TechnoSite');
         }

         return $this->er;
     }
}