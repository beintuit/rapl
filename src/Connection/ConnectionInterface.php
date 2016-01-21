<?php

namespace RAPL\RAPL\Connection;

interface ConnectionInterface
{
    /**
     * @param string $method
     * @param string $uri
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri);
}
