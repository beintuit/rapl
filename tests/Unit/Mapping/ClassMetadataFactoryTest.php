<?php

namespace RAPL\Tests\Unit\Mapping;

use Mockery;
use PHPUnit_Framework_TestCase;
use RAPL\RAPL\Mapping\ClassMetadataFactory;

class ClassMetadataFactoryTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'RAPL\Tests\Fixtures\Entities\Book';

    CONST CLASS_ALIAS = 'Foo:Book';

    /**
     * @var Mockery\MockInterface
     */
    private $mappingDriver;

    /**
     * @var Mockery\MockInterface
     */
    private $configuration;

    /**
     * @var Mockery\MockInterface|\RAPL\RAPL\EntityManager
     */
    private $entityManager;

    /**
     * @var ClassMetadataFactory
     */
    private $classMetadataFactory;

    protected function setUp()
    {
        $this->mappingDriver = Mockery::mock('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver');

        $this->configuration = Mockery::mock('RAPL\RAPL\Configuration');
        $this->configuration->shouldReceive('getMetadataDriver')->andReturn($this->mappingDriver);

        $this->entityManager = Mockery::mock('RAPL\RAPL\EntityManager');
        $this->entityManager->shouldReceive('getConfiguration')->andReturn($this->configuration);

        $this->classMetadataFactory = new ClassMetadataFactory();
        $this->classMetadataFactory->setEntityManager($this->entityManager);
    }

    public function testGetMetadataForReturnsClassMetadata()
    {
        $this->mappingDriver->shouldReceive('loadMetadataForClass')->once();

        $this->classMetadataFactory->getMetadataFor(self::CLASS_NAME);
    }

    public function testGetMetadataForResolvesAlias()
    {
        $this->mappingDriver->shouldReceive('loadMetadataForClass')->once();

        $this->configuration
            ->shouldReceive('getEntityNamespace')
            ->once()
            ->with('Foo')
            ->andReturn('RAPL\Tests\Fixtures\Entities');

        $this->classMetadataFactory->getMetadataFor(self::CLASS_ALIAS);
    }

    public function testGetMetadataForRethrowsReflectionExceptions()
    {
        $this->mappingDriver->shouldReceive('loadMetadataForClass')->andThrow('ReflectionException');

        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $this->classMetadataFactory->getMetadataFor(self::CLASS_NAME);
    }
}
