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
     * @return RequestInterface
     */
    public function createRequest($method, $uri);

    /**
     * @param RequestInterface $request
     *
     * @return Response
     */
    public function sendRequest(RequestInterface $request);
}
