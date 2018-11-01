<?php
/**
 * Created by PhpStorm.
 * User: sergeyro
 * Date: 01.11.18
 * Time: 10:54
 */

namespace HttpsController;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpsVpnController
 * @package HttpsController
 */
class HttpsVpnController
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var array
     */
    private static $headers = [
        'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0',
        'Connection' => 'keep-alive',
        'Upgrade-Insecure-Requests' => '1',
    ];

    public function __construct(array $guzzleConfig)
    {
        $guzzleNewConfig = $this->getGuzzleConfiguration($guzzleConfig);
        $this->httpClient = new \GuzzleHttp\Client($guzzleNewConfig);
    }

    private function getGuzzleConfiguration(array $guzzleConfig): array
    {
        if (empty($guzzleConfig)) {
            $guzzleConfig = [
                'timeout' => 50,
                'headers' => self::$headers,
                'cookies' => true,
                'expect' => false,
                'verify' => false,
                'http_errors' => false,
                'debug' => false,
                'allow_redirects' => true,

//                'proxy' => 'tcp://localhost:8888'
//                'curl' => [
//                    CURLOPT_INTERFACE => 'tun0',
//                ],
            ];
            return $guzzleConfig;
        }
        return $guzzleConfig;
    }

    /**
     * @return array|null
     */
    private function getLiveDockerVPNLinks():?array {
        $out = [];
        exec('docker ps -q | xargs docker inspect --format \'{{ .NetworkSettings.Ports }}\'', $out);
        if  (!empty($out)){
            $finish = [];
            foreach ($out as $item) {
                $flag = preg_match('/map\[80\/tcp\:\[\{127\.0\.0\.1 5.*?}]]/im', $item);
                if  ($flag){
                    $finish[] = str_replace(' ', ':', str_replace('}]]', '', str_replace('map[80/tcp:[{', '', $item)));
                }
            }
            return $finish;
        }
        return null;
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
    public function getVPNGuzzleClient(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client($this->httpClient->getConfig());
    }

    /**
     * @param $link
     * @return ResponseInterface
     */

    public function get($link): ?ResponseInterface
    {
        $ips = $this->getLiveDockerVPNLinks();
        if ($ips!==null){
            $ip = array_rand($ips);
            return $this->httpClient->get('http://'. $ips[$ip] .'/?get=' . $link);
        }
        return null;
    }
}