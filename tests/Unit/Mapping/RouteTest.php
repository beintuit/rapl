<?php

namespace RAPL\Tests\Unit\Mapping;

use PHPUnit_Framework_TestCase;
use RAPL\RAPL\Mapping\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testRoute()
    {
        $pattern   = 'foo/bar/{id}';
        $envelopes = ['foo', 'bar'];

        $route = new Route($pattern, false, $envelopes);

        $this->assertSame($pattern, $route->getPattern());
        $this->assertSame($envelopes, $route->getEnvelopes());
        $this->assertFalse($route->returnsCollection());
    }
}
