<?php

namespace RAPL\Tests\Unit\Routing;

use RAPL\RAPL\Mapping\Route;
use RAPL\RAPL\Routing\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var \Mockery\MockInterface
     */
    private $classMetadata;

    protected function setUp()
    {
        $this->router        = new Router();
        $this->classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
    }

    public function testGenerateWithoutConditionsReturnsCollectionUri()
    {
        $this->classMetadata->shouldReceive('hasRoute')->once()->with('resource')->andReturn(true);
        $this->classMetadata->shouldReceive('getIdentifierFieldNames')->once()->andReturn(array('id'));
        $this->classMetadata->shouldReceive('hasRoute')->once()->with('collection')->andReturn(true);
        $this->classMetadata->shouldReceive('getRoute')->once()->with('collection')->andReturn(new Route('books'));

        $this->assertSame('books', $this->router->generate($this->classMetadata));
    }

    public function testGenerateWithIdentifierAsConditionReturnsResourceUri()
    {
        $this->classMetadata->shouldReceive('hasRoute')->once()->with('resource')->andReturn(true);
        $this->classMetadata->shouldReceive('getIdentifierFieldNames')->atLeast(1)->andReturn(array('id'));
        $this->classMetadata->shouldReceive('getRoute')->once()->with('resource')->andReturn(new Route('books/{id}'));
        $this->classMetadata->shouldReceive('getSerializedName')->with('id')->andReturn('id');

        $this->assertSame('books/3', $this->router->generate($this->classMetadata, array('id' => 3)));
    }

    public function testGenerateWithNonIdentifierConditionsReturnsCollectionUriWithQueryString()
    {
        $this->classMetadata->shouldReceive('hasRoute')->once()->with('resource')->andReturn(true);
        $this->classMetadata->shouldReceive('getIdentifierFieldNames')->atLeast(1)->andReturn(array('id'));
        $this->classMetadata->shouldReceive('getSerializedName')->once()->with('title')->andReturn('serialized_title');

        $this->classMetadata->shouldReceive('hasRoute')->once()->with('collection')->andReturn(true);
        $this->classMetadata->shouldReceive('getRoute')->once()->with('collection')->andReturn(new Route('books'));

        $this->assertSame(
            'books?serialized_title=Foo',
            $this->router->generate($this->classMetadata, array('title' => 'Foo'))
        );
    }

    public function testGenerateWithMissingRouteConfigurationThrowsException()
    {
        $this->classMetadata->shouldReceive('hasRoute')->once()->with('resource')->andReturn(false);
        $this->classMetadata->shouldReceive('hasRoute')->once()->with('collection')->andReturn(false);
        $this->classMetadata->shouldReceive('getName')->once()->andReturn('Foo\Bar');

        $this->setExpectedException(
            'RAPL\RAPL\Mapping\MappingException',
            'A collection route is not configured for class Foo\Bar.'
        );

        $this->router->generate($this->classMetadata, array());
    }
}
