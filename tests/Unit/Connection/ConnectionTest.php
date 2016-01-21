<?php

namespace RAPL\Tests\Unit\Connection;

use GuzzleHttp\Middleware;
use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use RAPL\RAPL\Connection\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://example.com/api/';

    const REQUEST_METHOD = 'GET';

    const REQUEST_URI = 'foo/bar';

    /**
     * @var Mockery\MockInterface|\GuzzleHttp\ClientInterface
     */
    private $guzzleClient;

    /**
     * @var Connection
     */
    private $connection;

    protected function setUp()
    {
        $this->guzzleClient = Mockery::mock('GuzzleHttp\ClientInterface');
        $this->connection   = new Connection($this->guzzleClient);
    }

    public function testCreateReturnsConnectionInstance()
    {
        $middleware = [
            Middleware::mapRequest(function (RequestInterface $request) {
                return $request->withHeader('X-Foo', 'bar');
            })
        ];

        $actual = Connection::create(self::BASE_URL, $middleware);

        $this->assertInstanceOf('RAPL\RAPL\Connection\Connection', $actual);
    }

    public function testRequestCallsRequestOnGuzzleClient()
    {
        /** @var Mockery\MockInterface|\Psr\Http\Message\ResponseInterface $response */
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');

        $this->guzzleClient
            ->shouldReceive('request')
            ->once()
            ->with(self::REQUEST_METHOD, self::REQUEST_URI)
            ->andReturn($response);

        $actual = $this->connection->request(self::REQUEST_METHOD, self::REQUEST_URI);

        $this->assertSame($response, $actual);
    }
}
