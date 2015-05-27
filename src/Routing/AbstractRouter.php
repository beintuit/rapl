<?php

namespace RAPL\RAPL\Routing;

use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Mapping\MappingException;
use RAPL\RAPL\Mapping\Route;

abstract class AbstractRouter implements RouterInterface
{
    /**
     * @param ClassMetadata $classMetadata
     * @param array         $conditions
     * @param array         $orderBy
     * @param int|null      $limit
     * @param int|null      $offset
     *
     * @return string
     * @throws MappingException
     */
    public function generate(
        ClassMetadata $classMetadata,
        array $conditions = array(),
        array $orderBy = array(),
        $limit = null,
        $offset = null
    ) {
        $query = new Query($conditions, $orderBy, $limit, $offset);

        $route = $this->selectRoute($classMetadata, $query);

        $path = $this->buildPath($route->getPattern(), $query);

        $mappedQuery = new Query(
            $this->mapKeys($classMetadata, $query->getConditions()),
            $this->mapKeys($classMetadata, $query->getOrderBy()),
            $query->getLimit(),
            $query->getOffset()
        );

        $queryString = $this->buildQueryString($mappedQuery);

        if (empty($queryString)) {
            return $path;
        } else {
            return $path.'?'.$queryString;
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param array         $array
     *
     * @return array
     */
    private function mapKeys(ClassMetadata $classMetadata, array $array)
    {
        $array = array_flip($array);

        $array = array_map(
            function ($fieldName) use ($classMetadata) {
                return $classMetadata->getSerializedName($fieldName);
            },
            $array
        );

        return array_flip($array);
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param array         $conditions
     * @param array         $orderBy
     * @param int|null      $limit
     * @param int|null      $offset
     *
     * @return Route
     * @throws MappingException
     */
    public function getRoute(
        ClassMetadata $classMetadata,
        array $conditions = array(),
        array $orderBy = array(),
        $limit = null,
        $offset = null
    ) {
        $query = new Query($conditions, $orderBy, $limit, $offset);

        return $this->selectRoute($classMetadata, $query);
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param Query         $query
     *
     * @return Route
     * @throws MappingException
     */
    protected function selectRoute(ClassMetadata $classMetadata, Query $query)
    {
        if ($this->canUseResourceRoute($classMetadata, $query)) {
            return $classMetadata->getRoute('resource');
        } elseif ($classMetadata->hasRoute('collection')) {
            return $classMetadata->getRoute('collection');
        }

        throw MappingException::routeNotConfigured($classMetadata->getName(), 'collection');
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param Query         $query
     *
     * @return bool
     */
    protected function canUseResourceRoute(ClassMetadata $classMetadata, Query $query)
    {
        if (!$classMetadata->hasRoute('resource')) {
            return false;
        }

        if (count($query->getConditions()) !== count($classMetadata->getIdentifierFieldNames())) {
            return false;
        }

        foreach ($classMetadata->getIdentifierFieldNames() as $idField) {
            if (!array_key_exists($idField, $query->getConditions())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $pattern
     * @param Query  $query
     *
     * @return string
     */
    protected function buildPath($pattern, Query $query)
    {
        $path = $pattern;

        foreach ($query->getConditions() as $parameter => $value) {
            $count = 0;
            $path  = str_replace(sprintf('{%s}', $parameter), $value, $path, $count);

            if ($count > 0) {
                $query->removeCondition($parameter);
            }
        }

        return $path;
    }

    /**
     * @param Query $query
     *
     * @return string
     */
    abstract protected function buildQueryString(Query $query);
}
