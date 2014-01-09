<?php

namespace Application\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use Application\Entity\TechnoSite;
use Application\Entity\Techno;
use Application\Entity\TechnoCategory;

class TechnoTop  extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager;

    protected $em;
    protected $topSiteER;
    protected $technoSiteER;
    protected $technoER;
    protected $technoCategoryER;

    protected $pagination;
    protected $startNum;
    protected $countryCode;

    /**
     * Analyze the technos of a website
     */
    public function batchAnalyze($num=50, $country='FR', $start=1) {
        $alexaTopSites = $this->getAlexaTopSites($num, $country, $start);
        $websites = array();
        $i=1;
        $limit = min($num, 100);

        echo "nombre : " . $num . "\n" ;
        foreach($alexaTopSites as $site) {
            if($i%$limit == 0){
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
            //echo "analyse : " . $site->getUrl() . "\n";
            $websites[$site->getUrl()] = $this->analyze('http://'.$site->getUrl());
            //print_r($websites[$site->getUrl()]);
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
                
                if (isset($app['pages'])) {
                    $pages = $this->parse($app['pages']);
                    //print_r($pages);
                    forEach ($pages as $k=>$page) {
                        $r = new \Zend\Http\Request();
                        $call->setUri($url.str_replace('\/', '/', $page));
                        $response = false; 
                        try{
                            $response = $client->dispatch($call);
                        } catch (\Exception $ex) {
                
                        }
                        
                        if ($response && $response->isSuccess() && $response->getStatusCode() == 200){
                            $technos[$appId]['page'][] = $page;
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

        return $technos;
    }

    /**
     * Analyze the categories based on json Wappalyzer file
     */
    public function updateTechnoCategory() {
        $reader = new \Zend\Config\Reader\Json();
        $data = $reader->fromFile(__DIR__ . '/../../../config/apps.json');

        //Updating categories
        foreach ($data['categories'] as $id => $label) {
            $technoCategory = $this->getTechnoCategoryRepository()->findOneBy(array('id' => $id));
            if (!$technoCategory) {
                $technoCategory = new TechnoCategory($id);
            }
            $technoCategory->setLabel($label);
            $this->getEntityManager()->persist($technoCategory);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        // Updating technos linked to categories
        foreach ($data['apps'] as $technoId => $data) {
            
            $techno = $this->getTechnoRepository()->findOneBy(array('techno' => $technoId));
            if (!$techno) {
                $techno = new Techno($technoId);
            }
            if(isset($data['website'])){
                $techno->setWebsite($data['website']);
            }

            foreach($data['cats'] as $k=>$v){
                $technoCategory = $this->getTechnoCategoryRepository()->findOneBy(array('id' => $v));
                if ($technoCategory) {
                    // if the association already exist, I remove it.
                    $techno->getCategories()->removeElement($technoCategory);
                    $techno->addCategory($technoCategory);
                }
            }

            $this->getEntityManager()->persist($techno);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();


        return true;
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
    public function getAlexaTopSites($maxResults = 50, $country='FR', $start=1)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('SELECT a FROM AlexaTopSites\Entity\AlexaTopSite a
                WHERE a.country = :country
                AND a.countryRank >= :start
                ORDER BY a.countryRank ASC');
        $query->setParameter('country', $country);
        $query->setParameter('start', $start);
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
         if (!$this->topSiteER) {
             $this->topSiteER = $this->getEntityManager()->getRepository('AlexaTopSites\Entity\AlexaTopSite');
         }

         return $this->topSiteER;
     }

     public function getTechnoSiteRepository()
     {
         if (!$this->technoSiteER) {
             $this->technoSiteER = $this->getEntityManager()->getRepository('Application\Entity\TechnoSite');
         }

         return $this->technoSiteER;
     }

     public function getTechnoCategoryRepository()
     {
         if (!$this->technoCategoryER) {
             $this->technoCategoryER = $this->getEntityManager()->getRepository('Application\Entity\TechnoCategory');
         }

         return $this->technoCategoryER;
     }

     public function getTechnoRepository()
     {
         if (!$this->technoER) {
             $this->technoER = $this->getEntityManager()->getRepository('Application\Entity\Techno');
         }

         return $this->technoER;
     }
}