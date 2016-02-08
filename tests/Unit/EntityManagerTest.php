<?php

namespace RAPL\Tests\Unit;

use Mockery;
use PHPUnit_Framework_TestCase;
use RAPL\RAPL\Client\HttpClient;
use RAPL\RAPL\Configuration;
use RAPL\RAPL\EntityRepository;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\UnitOfWork;
use RAPL\Tests\Mocks\EntityManagerMock;

class EntityManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mockery\MockInterface|EntityRepository
     */
    private $repository;

    /**
     * @var Mockery\MockInterface|HttpClient
     */
    private $httpClient;

    /**
     * @var Mockery\MockInterface|Configuration
     */
    private $configuration;

    /**
     * @var Mockery\MockInterface|UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var EntityManagerMock
     */
    private $entityManager;

    protected function setUp()
    {
        $this->repository = Mockery::mock('RAPL\RAPL\EntityRepository');

        $repositoryFactory = Mockery::mock('RAPL\RAPL\Repository\RepositoryFactory');
        $repositoryFactory->shouldReceive('getRepository')->andReturn($this->repository);

        $this->httpClient = Mockery::mock(HttpClient::class);

        $this->configuration = Mockery::mock('RAPL\RAPL\Configuration');
        $this->configuration->shouldReceive('getRepositoryFactory')->andReturn($repositoryFactory);

        $router = Mockery::mock('RAPL\RAPL\Routing\Router');

        $this->entityManager = new EntityManagerMock($this->httpClient, $this->configuration, $router);

        $this->unitOfWork = Mockery::mock('RAPL\RAPL\UnitOfWork');
        $this->entityManager->setUnitOfWork($this->unitOfWork);
    }

    public function testFind()
    {
        $id     = 3;
        $object = new \stdClass();

        $this->repository->shouldReceive('find')->once()->with($id)->andReturn($object);

        $this->assertSame($object, $this->entityManager->find('SomeClass', $id));
    }

    public function testPersist()
    {
        $object = new \stdClass();

        $this->unitOfWork->shouldReceive('persist')->once()->with($object);

        $this->entityManager->persist($object);
    }

    public function testRemove()
    {
        $object = new \stdClass();

        $this->unitOfWork->shouldReceive('remove')->once()->with($object);

        $this->entityManager->remove($object);
    }

    public function testMerge()
    {
        $object        = new \stdClass();
        $managedObject = new \stdClass();

        $this->unitOfWork->shouldReceive('merge')->once()->with($object)->andReturn($managedObject);

        $this->assertSame($managedObject, $this->entityManager->merge($object));
    }

    public function testClear()
    {
        $this->unitOfWork->shouldReceive('clear')->once()->with(null);

        $this->entityManager->clear();
    }

    public function testClearSpecificEntity()
    {
        $this->unitOfWork->shouldReceive('clear')->once()->with('EntityName');

        $this->entityManager->clear('EntityName');
    }

    public function testDetach()
    {
        $object = new \stdClass();

        $this->unitOfWork->shouldReceive('detach')->once()->with($object);

        $this->entityManager->detach($object);
    }

    public function testRefresh()
    {
        $object = new \stdClass();

        $this->unitOfWork->shouldReceive('refresh')->once()->with($object);

        $this->entityManager->refresh($object);
    }

    public function testFlush()
    {
        $this->unitOfWork->shouldReceive('commit')->once();

        $this->entityManager->flush();
    }

    public function testGetRepository()
    {
        $className = 'SomeClass';

        $this->assertSame($this->repository, $this->entityManager->getRepository($className));
    }

    public function testGetMetadataFactory()
    {
        $this->assertInstanceOf('RAPL\RAPL\Mapping\ClassMetadataFactory', $this->entityManager->getMetadataFactory());
    }

    public function testGetClassMetadata()
    {
        $className = 'SomeClass';

        $classMetadata = new ClassMetadata($className);

        $classMetadataFactory = Mockery::mock('RAPL\RAPL\Mapping\ClassMetadataFactory');
        $classMetadataFactory->shouldReceive('getMetadataFor')->once()->with($className)->andReturn($classMetadata);

        $this->entityManager->setMetadataFactory($classMetadataFactory);

        $this->assertSame($classMetadata, $this->entityManager->getClassMetadata($className));
    }

    public function testInitializeObject()
    {
        $object = new \stdClass();

        $this->unitOfWork->shouldReceive('initializeObject')->withArgs([$object])->once();

        $this->entityManager->initializeObject($object);
    }

    public function testContains()
    {
        $object = new \stdClass();

        $this->unitOfWork->shouldReceive('isScheduledForInsert')->withArgs([$object])->andReturn(false)->once();
        $this->unitOfWork->shouldReceive('isInIdentityMap')->withArgs([$object])->andReturn(true)->once();
        $this->unitOfWork->shouldReceive('isScheduledForDelete')->withArgs([$object])->andReturn(false)->once();

        $this->assertTrue($this->entityManager->contains($object));
    }

    public function testGetConnection()
    {
        $this->assertSame($this->httpClient, $this->entityManager->getHttpClient());
    }

    public function testGetConfiguration()
    {
        $this->assertSame($this->configuration, $this->entityManager->getConfiguration());
    }

    public function testGetUnitOfWork()
    {
        $this->assertSame($this->unitOfWork, $this->entityManager->getUnitOfWork());
    }
}
