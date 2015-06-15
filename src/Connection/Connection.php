<?php

namespace RAPL\RAPL\Connection;

use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

class Connection implements ConnectionInterface
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->guzzleClient = new Client($baseUrl);
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri)
    {
        return $this->guzzleClient->createRequest($method, $uri);
    }

    /**
     * @param RequestInterface $request
     *
     * @return Response
     */
    public function sendRequest(RequestInterface $request)
    {
        return $request->send();
    }
}
