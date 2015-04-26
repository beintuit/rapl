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

        $this->classMetadata->shouldReceive('hasRoute')->with('resource')->andReturn(true);
        $this->classMetadata->shouldReceive('hasRoute')->with('collection')->andReturn(true);

        $this->classMetadata->shouldReceive('getIdentifierFieldNames')->andReturn(array('id'));

        $this->classMetadata->shouldReceive('getRoute')->with('resource')->andReturn(new Route('books/{id}'));
        $this->classMetadata->shouldReceive('getRoute')->with('collection')->andReturn(new Route('books'));

        $this->classMetadata->shouldReceive('getSerializedName')->with('id')->andReturn('book_id');
        $this->classMetadata->shouldReceive('getSerializedName')->with('title')->andReturn('serialized_title');
    }

    public function testGenerateWithoutConditionsReturnsCollectionUri()
    {
        $this->assertSame('books', $this->router->generate($this->classMetadata));
    }

    public function testGenerateWithIdentifierAsConditionReturnsResourceUri()
    {
        $this->assertSame('books/3', $this->router->generate($this->classMetadata, array('id' => 3)));
    }

    public function testGenerateWithNonIdentifierConditionsReturnsCollectionUriWithQueryString()
    {
        $this->assertSame(
            'books?serialized_title=Foo',
            $this->router->generate($this->classMetadata, array('title' => 'Foo'))
        );
    }

    public function testGenerateWithMissingRouteConfigurationThrowsException()
    {
        $classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');

        $classMetadata->shouldReceive('hasRoute')->once()->with('resource')->andReturn(false);
        $classMetadata->shouldReceive('hasRoute')->once()->with('collection')->andReturn(false);
        $classMetadata->shouldReceive('getName')->once()->andReturn('Foo\Bar');

        $this->setExpectedException(
            'RAPL\RAPL\Mapping\MappingException',
            'A collection route is not configured for class Foo\Bar.'
        );

        $this->router->generate($classMetadata, array());
    }
}
