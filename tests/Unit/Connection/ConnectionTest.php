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
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');
        $request->shouldReceive('send')->once();

        $this->connection->sendRequest($request);
    }

    public function testAddSubscriber()
    {
        $subscriber = \Mockery::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $subscriber->shouldReceive('getSubscribedEvents')->once()->andReturn(array());

        $this->connection->addSubscriber($subscriber);
    }
}
