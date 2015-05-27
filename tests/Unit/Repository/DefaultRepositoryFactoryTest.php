<?php

namespace RAPL\Tests\Unit\Repository;

use RAPL\RAPL\Repository\DefaultRepositoryFactory;

class DefaultRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'SomeClass';

    /**
     * @var DefaultRepositoryFactory
     */
    private $factory;

    /**
     * @var \Mockery\MockInterface
     */
    private $unitOfWork;

    /**
     * @var \Mockery\MockInterface
     */
    private $entityManager;

    protected function setUp()
    {
        $this->factory = new DefaultRepositoryFactory();

        $classMetadata = \Mockery::mock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $classMetadata->shouldReceive('getName')->andReturn(self::CLASS_NAME);

        $persister = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');

        $this->unitOfWork = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $this->unitOfWork->shouldReceive('getEntityPersister')->with(self::CLASS_NAME)->andReturn($persister);

        $this->entityManager = \Mockery::mock('RAPL\RAPL\EntityManagerInterface');
        $this->entityManager->shouldReceive('getUnitOfWork')->andReturn($this->unitOfWork);
        $this->entityManager->shouldReceive('getClassMetadata')->andReturn($classMetadata);
    }

    public function testGetRepositoryReturnsRepositoryInstance()
    {
        $repository = $this->factory->getRepository($this->entityManager, self::CLASS_NAME);
        $this->assertInstanceOf('RAPL\RAPL\EntityRepository', $repository);
    }

    public function testCallingGetRepositoryTwiceReturnsTheSameInstance()
    {
        $repository  = $this->factory->getRepository($this->entityManager, self::CLASS_NAME);
        $repository2 = $this->factory->getRepository($this->entityManager, self::CLASS_NAME);

        $this->assertSame($repository, $repository2);
    }
}
