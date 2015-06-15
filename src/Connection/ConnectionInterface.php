<?php

namespace RAPL\RAPL\Connection;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

interface ConnectionInterface
{
    /**
     * @param string $method
     * @param string $uri
     *
     * @return Response
     */
    public function request($method, $uri);

    /**
     * @param string $method
     * @param string $uri
     *
     * @return RequestInterface
     *
     * @deprecated
     */
    public function createRequest($method, $uri);

    /**
     * @param RequestInterface $request
     *
     * @return Response
     *
     * @deprecated
     */
    public function sendRequest(RequestInterface $request);
}
