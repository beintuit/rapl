<?php

namespace RAPL\RAPL\Client;

interface HttpClient
{
    /**
     * @param string $method
     * @param string $uri
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri);
}
