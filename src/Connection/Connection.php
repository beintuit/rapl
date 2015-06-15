<?php

namespace RAPL\RAPL\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\RequestInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @param ClientInterface $guzzleClient
     */
    public function __construct(ClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param string $baseUrl
     *
     * @return Connection
     */
    public static function create($baseUrl)
    {
        return new self(new Client(['base_uri' => $baseUrl]));
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function request($method, $uri)
    {
        $request = $this->guzzleClient->createRequest($method, $uri);

        return $this->guzzleClient->send($request);
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return \GuzzleHttp\Message\RequestInterface
     *
     * @deprecated
     */
    public function createRequest($method, $uri)
    {
        return $this->guzzleClient->createRequest($method, $uri);
    }

    /**
     * @param RequestInterface $request
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     *
     * @deprecated
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->guzzleClient->send($request);
    }
}
