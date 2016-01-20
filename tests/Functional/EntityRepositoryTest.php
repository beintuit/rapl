<?php

namespace RAPL\Tests\Functional;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use RAPL\RAPL\Configuration;
use RAPL\RAPL\Connection\ConnectionInterface;
use RAPL\RAPL\EntityManager;
use RAPL\RAPL\EntityRepository;
use RAPL\RAPL\Mapping\Driver\YamlDriver;
use RAPL\RAPL\Routing\Router;
use RAPL\Tests\Fixtures\Entities\Book;

class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'RAPL\Tests\Fixtures\Entities\Book';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var \Mockery\MockInterface|ConnectionInterface
     */
    private $connection;

    protected function setUp()
    {
        $this->connection = \Mockery::mock('RAPL\RAPL\Connection\ConnectionInterface');

        $configuration = new Configuration();
        $paths         = [__DIR__.'/../Fixtures/config'];
        $driver        = new YamlDriver($paths, '.rapl.yml');
        $configuration->setMetadataDriver($driver);

        $this->entityManager = new EntityManager($this->connection, $configuration, new Router());

        $this->repository = $this->entityManager->getRepository(self::CLASS_NAME);
    }

    public function testFind()
    {
        $json = '{"results": [{
            "id": 1,
            "title": "Winnie the Pooh",
            "isbn": "1234567890123"
        }]}';

        $this->mockHttpRequestAndResponse('books/1', 200, $json);

        /** @var Book $actual */
        $actual = $this->repository->find(1);

        $this->assertInstanceOf(self::CLASS_NAME, $actual);
        $this->assertSame('Winnie the Pooh', $actual->getTitle());
    }

    public function testFindNonExistingEntityReturnsNull()
    {
        $this->mockHttpRequestAndResponse('books/123', 404);

        $this->assertNull($this->repository->find(123));
    }

    public function testFindThrowsOtherExceptions()
    {
        $this->mockHttpRequestAndResponse('books/1', 403);

        $this->setExpectedException('GuzzleHttp\Exception\ClientException');

        $this->repository->find(1);
    }

    public function testFindAll()
    {
        $json = '{"results": [
            {
                "id": 1,
                "title": "Winnie the Pooh",
                "isbn": "1234567890123"
            },
            {
                "id": 2,
                "title": "Moby Dick",
                "isbn": "9876543210321"
            },
            {
                "id": 3,
                "title": "Harry Potter",
                "isbn": "1968132132980"
            }
        ]}';

        $this->mockHttpRequestAndResponse('books', 200, $json);

        $actual = $this->repository->findAll();

        $this->assertSame(3, count($actual));
        $this->assertContainsOnlyInstancesOf('RAPL\Tests\Fixtures\Entities\Book', $actual);
    }

    public function testFindOneBy()
    {
        $json = '{"results": [
            {
                "id": 1,
                "title": "Winnie the Pooh",
                "isbn": "1234567890123"
            },
            {
                "id": 2,
                "title": "Moby Dick",
                "isbn": "9876543210321"
            },
            {
                "id": 3,
                "title": "Harry Potter",
                "isbn": "1968132132980"
            }
        ]}';

        $this->mockHttpRequestAndResponse('books', 200, $json);

        /** @var Book $actual */
        $actual = $this->repository->findOneBy([]);

        $this->assertInstanceOf('RAPL\Tests\Fixtures\Entities\Book', $actual);
        $this->assertSame('Winnie the Pooh', $actual->getTitle());
        $this->assertSame('1234567890123', $actual->getIsbn());
    }

    /**
     * @param string $uri
     * @param int    $responseCode
     * @param string $responseData
     */
    private function mockHttpRequestAndResponse($uri, $responseCode = 200, $responseData = '')
    {
        $request  = new Request('GET', $uri);
        $response = new Response($responseCode, [], $responseData);

        if ($responseCode >= 400) {
            $exception = RequestException::create($request, $response);
            $this->connection->shouldReceive('request')->once()->with('GET', $uri)->andThrow($exception);
        } else {
            $this->connection->shouldReceive('request')->once()->with('GET', $uri)->andReturn($response);
        }
    }
}
