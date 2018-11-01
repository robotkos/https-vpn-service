<?php
/**
 * Created by PhpStorm.
 * User: sergeyro
 * Date: 01.11.18
 * Time: 10:54
 */

use GuzzleHttp\ClientInterface;

class HttpsVpnController
{
    /**
     * @var ClientInterface
     */
    private $httpClient;


    private function __construct(array $guzzleConfig)
    {
        $this->httpClient = new \GuzzleHttp\Client($guzzleConfig);
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

//    /**
//     * @return \GuzzleHttp\Client
//     */
//    public function getGoutteClient()
//    {
//        $gouteClient = new \Goutte\Client();
//        $gouteClient->setClient($this->httpClient);
//        $userAgent = $this->httpClient
//            ->getConfig('headers')['User-Agent'];
//        $gouteClient->setHeader(
//            'user-agent',
//            $userAgent
//        );// fix bug in settings with goutte user agent
//        $gouteClient
//            ->setServerParameter("HTTP_USER_AGENT", $userAgent);
//        return $gouteClient;
//    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getVPNGuzzleClient(): \GuzzleHttp\Client{
        dump($this->httpClient->getConfig());
        return new \GuzzleHttp\Client($this->httpClient->getConfig());
    }
}