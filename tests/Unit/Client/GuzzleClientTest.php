<?php

namespace RAPL\Tests\Unit\Client;

use GuzzleHttp\Middleware;
use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use RAPL\RAPL\Client\GuzzleClient;

class GuzzleClientTest extends PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://example.com/api/';

    const REQUEST_METHOD = 'GET';

    const REQUEST_URI = 'foo/bar';

    /**
     * @var Mockery\MockInterface|\GuzzleHttp\ClientInterface
     */
    private $guzzleClient;

    /**
     * @var GuzzleClient
     */
    private $client;

    protected function setUp()
    {
        $this->guzzleClient = Mockery::mock('GuzzleHttp\ClientInterface');
        $this->client = new GuzzleClient($this->guzzleClient);
    }

    public function testCreateReturnsGuzzleClientInstance()
    {
        $middleware = [
            Middleware::mapRequest(function (RequestInterface $request) {
                return $request->withHeader('X-Foo', 'bar');
            })
        ];

        $actual = GuzzleClient::create(self::BASE_URL, $middleware);

        $this->assertInstanceOf(GuzzleClient::class, $actual);
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

        $actual = $this->client->request(self::REQUEST_METHOD, self::REQUEST_URI);

        $this->assertSame($response, $actual);
    }
}
