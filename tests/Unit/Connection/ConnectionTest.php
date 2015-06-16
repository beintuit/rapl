<?php

namespace RAPL\Tests\Unit\Connection;

use RAPL\RAPL\Connection\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://example.com/api/';

    const REQUEST_METHOD = 'GET';

    const REQUEST_URI = 'foo/bar';

    /**
     * @var \Mockery\MockInterface|\GuzzleHttp\ClientInterface
     */
    private $guzzleClient;

    /**
     * @var Connection
     */
    private $connection;

    protected function setUp()
    {
        $this->guzzleClient = \Mockery::mock('GuzzleHttp\ClientInterface');
        $this->connection   = new Connection($this->guzzleClient);
    }

    public function testCreateReturnsConnectionInstance()
    {
        $actual = Connection::create(self::BASE_URL);

        $this->assertInstanceOf('RAPL\RAPL\Connection\Connection', $actual);
    }

    public function testRequestReturnsResponseObject()
    {
        /** @var \Mockery\MockInterface|\GuzzleHttp\Message\RequestInterface $request */
        $request = \Mockery::mock('GuzzleHttp\Message\RequestInterface');

        /** @var \Mockery\MockInterface|\GuzzleHttp\Message\Response $response */
        $response = \Mockery::mock('GuzzleHttp\Message\Response');

        $this->guzzleClient->shouldReceive('createRequest')
            ->once()
            ->with(self::REQUEST_METHOD, self::REQUEST_URI)
            ->andReturn($request);

        $this->guzzleClient->shouldReceive('send')->once()->with($request)->andReturn($response);

        $actual = $this->connection->request(self::REQUEST_METHOD, self::REQUEST_URI);

        $this->assertSame($response, $actual);
    }

    public function testCreateRequestReturnsRequestObject()
    {
        /** @var \Mockery\MockInterface|\GuzzleHttp\Message\RequestInterface $request */
        $request = \Mockery::mock('GuzzleHttp\Message\RequestInterface');

        $this->guzzleClient->shouldReceive('createRequest')
            ->once()
            ->with(self::REQUEST_METHOD, self::REQUEST_URI)
            ->andReturn($request);

        $actual = $this->connection->createRequest('GET', 'foo/bar');

        $this->assertSame($request, $actual);
    }

    public function testSendRequestInvokesSendMethodOnRequest()
    {
        /** @var \Mockery\MockInterface|\GuzzleHttp\Message\RequestInterface $request */
        $request = \Mockery::mock('GuzzleHttp\Message\RequestInterface');

        /** @var \Mockery\MockInterface|\GuzzleHttp\Message\Response $response */
        $response = \Mockery::mock('GuzzleHttp\Message\Response');

        $this->guzzleClient->shouldReceive('send')->once()->with($request)->andReturn($response);

        $actual = $this->connection->sendRequest($request);

        $this->assertSame($response, $actual);
    }
}
