<?php

namespace RAPL\RAPL\Connection;

use Guzzle\Http\Message\RequestInterface;

interface ConnectionInterface
{
    /**
     * @param string $method
     * @param string $uri
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function request($method, $uri);

    /**
     * @param string $method
     * @param string $uri
     *
     * @return \Guzzle\Http\Message\RequestInterface
     *
     * @deprecated
     */
    public function createRequest($method, $uri);

    /**
     * @param RequestInterface $request
     *
     * @return \Guzzle\Http\Message\Response
     *
     * @deprecated
     */
    public function sendRequest(RequestInterface $request);
}
