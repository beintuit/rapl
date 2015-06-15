<?php

namespace RAPL\Tests\Unit\Connection;

use RAPL\RAPL\Connection\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://example.com/api/';

    const REQUEST_METHOD = 'GET';

    const REQUEST_URI = 'foo/bar';

    /**
     * @var \Mockery\MockInterface|\Guzzle\Http\ClientInterface
     */
    private $guzzleClient;

    /**
     * @var Connection
     */
    private $connection;

    protected function setUp()
    {
        $this->guzzleClient = \Mockery::mock('Guzzle\Http\ClientInterface');
        $this->connection   = new Connection($this->guzzleClient);
    }

    public function testRequestReturnsResponseObject()
    {
        /** @var \Mockery\MockInterface|\Guzzle\Http\Message\RequestInterface $request */
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');

        /** @var \Mockery\MockInterface|\Guzzle\Http\Message\Response $response */
        $response = \Mockery::mock('Guzzle\Http\Message\Response');

        $this->guzzleClient->shouldReceive('createRequest')
            ->once()
            ->with(self::REQUEST_METHOD, self::REQUEST_URI)
            ->andReturn($request);

        $request->shouldReceive('send')->once()->andReturn($response);

        $actual = $this->connection->request(self::REQUEST_METHOD, self::REQUEST_URI);

        $this->assertSame($response, $actual);
    }

    public function testCreateRequestReturnsRequestObject()
    {
        /** @var \Mockery\MockInterface|\Guzzle\Http\Message\RequestInterface $request */
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');

        $this->guzzleClient->shouldReceive('createRequest')
            ->once()
            ->with(self::REQUEST_METHOD, self::REQUEST_URI)
            ->andReturn($request);

        $actual = $this->connection->createRequest('GET', 'foo/bar');

        $this->assertSame($request, $actual);
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
