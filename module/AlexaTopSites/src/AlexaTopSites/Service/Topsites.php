<?php

namespace AlexaTopSites\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use AlexaTopSites\Entity\AlexaTopSite;

/**
 * Makes a request to ATS for the top 10 sites in a country
 */
class Topsites  extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager;

    protected $em;
    protected $er;

    protected $ActionName        = 'TopSites';
    protected $ResponseGroupName = 'Country';
    protected $ServiceHost      = 'ats.amazonaws.com';
    protected $SigVersion        = '2';
    protected $HashAlgorithm     = 'HmacSHA256';

    protected $pagination;
    protected $startNum;
    protected $accessKeyId;
    protected $secretAccessKey;
    protected $countryCode;

    /**
     * Get top sites from ATS
     */
    public function getTopSites($accessKeyId, $secretAccessKey, $countryCode, $total=100, $startNum = 1, $pagination = 100) {

        $iterations= (int)($total/$pagination);
        $reste = $total%$pagination;
        $found = 0;

        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
        $this->countryCode = $countryCode;

        for($i=0;$i<$iterations;$i++){
            $this->startNum = $pagination*$i + $startNum;
            $this->pagination = $pagination;

            $queryParams = $this->buildQueryParams();
            $sig = $this->generateSignature($queryParams);
            $url = 'http://' . $this->ServiceHost . '/?' . $queryParams .
                '&Signature=' . $sig;
            $ret = $this->makeRequest($url);

            $found += $this->parseResponse($ret);
            echo "found: " . $found . " \n";
        }

        if($reste > 0){
            $this->startNum = $pagination*$iterations + $startNum;
            $this->pagination = $reste;

            $queryParams = $this->buildQueryParams();
            $sig = $this->generateSignature($queryParams);
            $url = 'http://' . $this->ServiceHost . '/?' . $queryParams .
            '&Signature=' . $sig;
            $ret = $this->makeRequest($url);

            $found += $this->parseResponse($ret);
            echo "found: " . $found . " \n";
        }

        return $found;
    }

    /**
     * Builds an ISO 8601 timestamp for request
     */
    protected function getTimestamp() {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }

    /**
     * Builds the url for the request to ATS
     * The url will be urlencoded as per RFC 3986 and the uri params
     * will be in alphabetical order
     */
    protected function buildQueryParams() {
        $params = array(
            'Action'            => $this->ActionName,
            'ResponseGroup'     => $this->ResponseGroupName,
            'AWSAccessKeyId'    => $this->accessKeyId,
            'Timestamp'         => $this->getTimestamp(),
            'CountryCode'       => $this->countryCode,
            'Count'             => $this->pagination,
            'Start'             => $this->startNum,
            'SignatureVersion'  => $this->SigVersion,
            'SignatureMethod'   => $this->HashAlgorithm
        );
        ksort($params);
        $keyvalue = array();
        foreach($params as $k => $v) {
            $keyvalue[] = $k . '=' . rawurlencode($v);
        }
        return implode('&',$keyvalue);
    }

    /**
     * Makes an http request
     *
     * @param $url      URL to make request to
     * @return String   Result of request
     */
    protected function makeRequest($url) {
        //print_r("Making request to: \n$url\n");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Parses the XML response from ATS and bulk insert/update results in database
     *
     * @param String $response    xml response from ATS
     */
    protected function parseResponse($response) {

        $array = array();
        $xml = new \SimpleXMLElement($response,null, false, 'http://ats.amazonaws.com/doc/2005-11-21');
        $i=0;

        $countryCode = (string) $xml->Response->TopSitesResult->Alexa->TopSites->Country->CountryCode;

        foreach($xml->Response->TopSitesResult->Alexa->TopSites->Country->Sites->children('http://ats.amazonaws.com/doc/2005-11-21') as $site) {
            /*$array[$i]['dataUrl']                     = (string) $site->DataUrl;
            $array[$i]['rank']                        = (string) $site->Global->Rank;
            $array[$i]['country']                     = $countryCode;
            $array[$i]['countryRank']                 = (string) $site->Country->Rank;
            $array[$i]['countryReachPerMillion']      = (string) $site->Country->Reach->PerMillion;
            $array[$i]['countryPageViewsPerMillion']  = (string) $site->Country->PageViews->PerMillion;
            $array[$i]['countryPageViewsPerUser']     = (string) $site->Country->PageViews->PerUser;*/

            $url = (string) $site->DataUrl;

            $alexaTopSite = $this->getEntityRepository()->findOneBy(array('url' => $url, 'country' => $countryCode));
            if (!$alexaTopSite) {
                $alexaTopSite = new AlexaTopSite($url, $countryCode);
            }

            $alexaTopSite->setRank((string) $site->Global->Rank);
            $alexaTopSite->setCountryRank((string) $site->Country->Rank);
            $alexaTopSite->setCountryReachPerMillion((string) $site->Country->Reach->PerMillion);
            $alexaTopSite->setCountryPageViewsPerMillion((string) $site->Country->PageViews->PerMillion);
            $alexaTopSite->setCountryPageViewsPerUser((string) $site->Country->PageViews->PerUser);

            $this->getEntityManager()->persist($alexaTopSite);

            $i++;
        }

            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear(); // Detaches all objects from Doctrine!

        return $i;
    }

    /**
     * Generates a signature per RFC 2104
     *
     * @param String $queryParams query parameters to use in creating signature
     * @return String             signature
     */
    protected function generateSignature($queryParams) {
        $sign = "GET\n" . strtolower($this->ServiceHost) . "\n/\n". $queryParams;
        //echo "String to sign: \n" . $sign . "\n\n";
        $sig = base64_encode(hash_hmac('sha256', $sign, $this->secretAccessKey, true));
        return rawurlencode($sig);
    }

     /**
      * @return the unknown_type
      */
     public function getAccessKeyId() {
      return $this->accessKeyId;
     }

     /**
      * @param unknown_type $accessKeyId
      */
     public function setAccessKeyId($accessKeyId) {
      $this->accessKeyId = $accessKeyId;
      return $this;
     }

     /**
      * @return the unknown_type
      */
     public function getSecretAccessKey() {
      return $this->secretAccessKey;
     }

     /**
      * @param unknown_type $secretAccessKey
      */
     public function setSecretAccessKey($secretAccessKey) {
      $this->secretAccessKey = $secretAccessKey;
      return $this;
     }

     /**
      * @return the unknown_type
      */
     public function getCountryCode() {
      return $this->countryCode;
     }

     /**
      * @param unknown_type $countryCode
      */
     public function setCountryCode($countryCode) {
      $this->countryCode = $countryCode;
      return $this;
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
}