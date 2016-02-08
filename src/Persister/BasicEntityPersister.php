<?php

namespace RAPL\RAPL\Persister;

use GuzzleHttp\Exception\ClientException;
use RAPL\RAPL\Client\HttpClient;
use RAPL\RAPL\EntityManagerInterface;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Routing\RouterInterface;
use RAPL\RAPL\Serializer\Serializer;

class BasicEntityPersister implements EntityPersister
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EntityManagerInterface $manager
     * @param ClassMetadata          $classMetadata
     * @param RouterInterface        $router
     */
    public function __construct(EntityManagerInterface $manager, ClassMetadata $classMetadata, RouterInterface $router)
    {
        $this->manager       = $manager;
        $this->httpClient = $manager->getHttpClient();
        $this->classMetadata = $classMetadata;

        $this->serializer = new Serializer($classMetadata, $manager->getUnitOfWork(), $manager->getMetadataFactory());
        $this->router     = $router;
    }

    /**
     * Loads an entity by a list of field conditions.
     *
     * @param array       $conditions The conditions by which to load the entity.
     * @param object|null $entity     The entity to load the data into. If not specified, a new entity is created.
     * @param string      $type       Entity type, either 'resource' or 'collection'
     *
     * @return object|null The loaded and managed entity instance or NULL if the entity can not be found.
     */
    public function load(array $conditions, $entity = null, $type = 'collection')
    {
        $uri   = $this->getUri($conditions);
        $route = $this->getRoute($conditions);

        try {
            $response = $this->httpClient->request('GET', $uri);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                return null;
            } else {
                throw $e;
            }
        }

        $entities = $this->serializer->deserialize(
            $response->getBody(),
            $route->returnsCollection(),
            $route->getEnvelopes()
        );

        return $entities ? $entities[0] : null;
    }

    /**
     * Loads an entity by identifier.
     *
     * @param array       $identifier The entity identifier.
     * @param object|null $entity     The entity to load the data into. If not specified, a new entity is created.
     *
     * @return object|null The loaded and managed entity instance or NULL if the entity can not be found.
     */
    public function loadById(array $identifier, $entity = null)
    {
        return $this->load($identifier, $entity, 'resource');
    }

    /**
     * Loads a list of entities by a list of field conditions.
     *
     * @param array      $conditions
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array
     */
    public function loadAll(array $conditions = [], array $orderBy = [], $limit = null, $offset = null)
    {
        $uri      = $this->getUri($conditions, $orderBy, $limit, $offset);
        $route    = $this->getRoute($conditions, $orderBy, $limit, $offset);
        $response = $this->httpClient->request('GET', $uri);

        return $this->serializer->deserialize(
            $response->getBody(),
            $route->returnsCollection(),
            $route->getEnvelopes()
        );
    }

    /**
     * Returns an URI based on a set of criteria
     *
     * @param array    $conditions
     * @param array    $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return string
     */
    private function getUri(array $conditions, array $orderBy = [], $limit = null, $offset = null)
    {
        return $this->router->generate($this->classMetadata, $conditions, $orderBy, $limit, $offset);
    }

    /**
     * @param array    $conditions
     * @param array    $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return \RAPL\RAPL\Mapping\Route
     */
    private function getRoute(array $conditions, array $orderBy = [], $limit = null, $offset = null)
    {
        return $this->router->getRoute($this->classMetadata, $conditions, $orderBy, $limit, $offset);
    }
}
