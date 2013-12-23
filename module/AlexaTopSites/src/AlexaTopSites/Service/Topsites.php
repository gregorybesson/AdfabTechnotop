<?php

namespace PlaygroundDesign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Makes a request to ATS for the top 10 sites in a country
 */
class TopSites  extends EventProvider implements ServiceManagerAwareInterface
{

    protected static $ActionName        = 'TopSites';
    protected static $ResponseGroupName = 'Country';
    protected static $ServiceHost      = 'ats.amazonaws.com';
    protected static $NumReturn         = 10;
    protected static $StartNum          = 1;
    protected static $SigVersion        = '2';
    protected static $HashAlgorithm     = 'HmacSHA256';

    protected $accessKeyId;
    protected $secretAccessKey;
    protected $countryCode;

    /**
     * Get top sites from ATS
     */
    public function getTopSites() {

        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
        $this->countryCode = $countryCode;

        $queryParams = $this->buildQueryParams();
        $sig = $this->generateSignature($queryParams);
        $url = 'http://' . self::$ServiceHost . '/?' . $queryParams .
            '&Signature=' . $sig;
        $ret = self::makeRequest($url);
        self::parseResponse($ret);
    }

    /**
     * Builds an ISO 8601 timestamp for request
     */
    protected static function getTimestamp() {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }

    /**
     * Builds the url for the request to ATS
     * The url will be urlencoded as per RFC 3986 and the uri params
     * will be in alphabetical order
     */
    protected function buildQueryParams() {
        $params = array(
            'Action'            => self::$ActionName,
            'ResponseGroup'     => self::$ResponseGroupName,
            'AWSAccessKeyId'    => $this->accessKeyId,
            'Timestamp'         => self::getTimestamp(),
            'CountryCode'       => $this->countryCode,
            'Count'             => self::$NumReturn,
            'Start'             => self::$StartNum,
            'SignatureVersion'  => self::$SigVersion,
            'SignatureMethod'   => self::$HashAlgorithm
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
    protected static function makeRequest($url) {
        print_r("Making request to: \n$url\n");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Parses the XML response from ATS and echoes the DataUrl element
     * for each returned site
     *
     * @param String $response    xml response from ATS
     */
    protected static function parseResponse($response) {
        echo "\nSites: \n";
        $xml = new SimpleXMLElement($response,null, false,
                                    'http://ats.amazonaws.com/doc/2005-11-21');
        foreach($xml->Response->TopSitesResult->Alexa->TopSites->Country->Sites->children('http://ats.amazonaws.com/doc/2005-11-21') as $site) {
            echo $site->DataUrl . "\n";
        }
    }

    /**
     * Generates a signature per RFC 2104
     *
     * @param String $queryParams query parameters to use in creating signature
     * @return String             signature
     */
    protected function generateSignature($queryParams) {
        $sign = "GET\n" . strtolower(self::$ServiceHost) . "\n/\n". $queryParams;
        echo "String to sign: \n" . $sign . "\n\n";
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
}