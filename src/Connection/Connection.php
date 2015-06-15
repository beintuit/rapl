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
        return new self(new Client($baseUrl));
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return Response
     */
    public function request($method, $uri)
    {
        $request = $this->guzzleClient->createRequest($method, $uri);

        return $request->send();
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return RequestInterface
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
     * @return Response
     *
     * @deprecated
     */
    public function sendRequest(RequestInterface $request)
    {
        return $request->send();
    }
}
