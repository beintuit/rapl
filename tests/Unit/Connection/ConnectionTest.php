<?php

namespace RAPL\Tests\Unit\Connection;

use RAPL\RAPL\Connection\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://example.com/api/';

    /**
     * @var Connection
     */
    private $connection;

    protected function setUp()
    {
        $this->connection = new Connection(self::BASE_URL);
    }

    public function testCreateRequestReturnsRequestObject()
    {
        $request = $this->connection->createRequest('GET', 'foo/bar');

        $this->assertInstanceOf('Guzzle\Http\Message\RequestInterface', $request);
        $this->assertSame(self::BASE_URL.'foo/bar', $request->getUrl());
    }

    public function testSendRequestInvokesSendMethodOnRequest()
    {
        /** @var \Mockery\MockInterface|\Guzzle\Http\Message\RequestInterface $request */
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');

        /** @var \Mockery\MockInterface|\Guzzle\Http\Message\Response $response */
        $response = \Mockery::mock('Guzzle\Http\Message\Response');

        $request->shouldReceive('send')->once()->andReturn($response);

        $actual = $this->connection->sendRequest($request);

        $this->assertSame($response, $actual);
    }
}
