<?php

namespace RAPL\Tests\Unit\Routing;

use PHPUnit_Framework_TestCase;
use RAPL\RAPL\Routing\Query;

class QueryTest extends PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        $conditions = ['foo' => 'bar'];
        $orderBy    = ['foo' => 'desc'];
        $limit      = 5;
        $offset     = 0;

        $query = new Query($conditions, $orderBy, $limit, $offset);

        $this->assertSame($conditions, $query->getConditions());
        $this->assertSame($orderBy, $query->getOrderBy());
        $this->assertSame($limit, $query->getLimit());
        $this->assertSame($offset, $query->getOffset());
    }

    public function testRemoveCondition()
    {
        $conditions = ['foo' => 'bar', 'bar' => 'barbaz'];

        $query = new Query($conditions);

        $query->removeCondition('foo');
        $this->assertSame(['bar' => 'barbaz'], $query->getConditions());
    }
}
