<?php

namespace RAPL\Tests\Unit;

use RAPL\RAPL\EntityManager;
use RAPL\RAPL\UnitOfWork;
use RAPL\Tests\Fixtures\Entities\Book;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface|EntityManager
     */
    protected $entityManager;

    /**
     * @var \Mockery\MockInterface
     */
    private $classMetadata;

    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    const CLASS_NAME = 'RAPL\Tests\Fixtures\Entities\Book';

    protected function setUp()
    {
        $router              = \Mockery::mock('RAPL\RAPL\Routing\RouterInterface');
        $this->classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $this->classMetadata->shouldReceive('getIdentifierFieldNames')->andReturn(array('id'));
        $this->classMetadata->shouldReceive('newInstance')->andReturn(new Book());
        $this->classMetadata->shouldReceive('hasField')->with('id')->andReturn(true);
        $this->classMetadata->shouldReceive('hasField')->with('title')->andReturn(true);
        $this->classMetadata->shouldReceive('setFieldValue');
        $this->classMetadata->shouldReceive('getName')->andReturn(self::CLASS_NAME);

        $connection = \Mockery::mock('RAPL\RAPL\Connection\Connection');

        $this->entityManager = \Mockery::mock('RAPL\RAPL\EntityManager');
        $this->entityManager->shouldReceive('getConnection')->andReturn($connection);
        $this->entityManager
            ->shouldReceive('getClassMetadata')
            ->with(self::CLASS_NAME)
            ->andReturn($this->classMetadata);

        $this->unitOfWork = new UnitOfWork($this->entityManager, $router);

        $this->entityManager->shouldReceive('getUnitOfWork')->andReturn($this->unitOfWork);
    }

    public function testGetEntityPersister()
    {
        $metadataFactory = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadataFactory');
        $this->entityManager->shouldReceive('getMetadataFactory')->once()->andReturn($metadataFactory);

        $this->assertInstanceOf(
            'RAPL\RAPL\Persister\EntityPersister',
            $this->unitOfWork->getEntityPersister(self::CLASS_NAME)
        );
    }

    public function testCallingGetEntityPersisterTwiceReturnsTheSameInstance()
    {
        $metadataFactory = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadataFactory');
        $this->entityManager->shouldReceive('getMetadataFactory')->once()->andReturn($metadataFactory);

        $this->assertSame(
            $this->unitOfWork->getEntityPersister(self::CLASS_NAME),
            $this->unitOfWork->getEntityPersister(self::CLASS_NAME)
        );
    }

    public function testIsInIdentityMap()
    {
        $entityA = $this->unitOfWork->createEntity(self::CLASS_NAME, array('id' => 123, 'title' => 'Foo'));
        $entityB = new Book();

        $this->assertTrue($this->unitOfWork->isInIdentityMap($entityA));
        $this->assertFalse($this->unitOfWork->isInIdentityMap($entityB));
    }

    public function testAddToIdentityMapReturnsFalseIfEntityIsAlreadyInIdentityMap()
    {
        $entity = $this->unitOfWork->createEntity(self::CLASS_NAME, array('id' => 123, 'title' => 'Foo'));

        $this->assertFalse($this->unitOfWork->addToIdentityMap($entity));
    }

    public function testRemoveFromIdentityMapRemovesEntityFromIdentityMap()
    {
        $entity = $this->unitOfWork->createEntity(self::CLASS_NAME, array('id' => 123, 'title' => 'Foo'));

        $this->assertTrue($this->unitOfWork->removeFromIdentityMap($entity));
        $this->assertFalse($this->unitOfWork->isInIdentityMap($entity));
        $this->assertFalse($this->unitOfWork->removeFromIdentityMap($entity));
    }

    public function testCreateEntity()
    {
        $data = array(
            'id'    => 123,
            'title' => 'Foo Bar'
        );

        /** @var Book $actual */
        $actual = $this->unitOfWork->createEntity(self::CLASS_NAME, $data);

        $this->assertInstanceOf(self::CLASS_NAME, $actual);
    }

    public function testCallingCreateEntityTwiceReturnsSameInstance()
    {
        $data = array(
            'id'    => 123,
            'title' => 'Foo Bar'
        );

        $this->assertSame(
            $this->unitOfWork->createEntity(self::CLASS_NAME, $data),
            $this->unitOfWork->createEntity(self::CLASS_NAME, $data)
        );
    }
}
