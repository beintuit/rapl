<?php

namespace RAPL\RAPL\Connection;

use GuzzleHttp\Message\RequestInterface;

interface ConnectionInterface
{
    /**
     * @param string $method
     * @param string $uri
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function request($method, $uri);

    /**
     * @param string $method
     * @param string $uri
     *
     * @return \GuzzleHttp\Message\RequestInterface
     *
     * @deprecated
     */
    public function createRequest($method, $uri);

    /**
     * @param RequestInterface $request
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     *
     * @deprecated
     */
    public function sendRequest(RequestInterface $request);
}
