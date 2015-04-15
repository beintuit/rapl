<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\Type;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultType()
    {
        $this->assertInstanceOf('RAPL\RAPL\Types\StringType', Type::getType('string'));
    }

    public function testAddAndGetCustomType()
    {
        Type::addType('custom', 'RAPL\Tests\Fixtures\Types\CustomType');

        $this->assertInstanceOf('RAPL\Tests\Fixtures\Types\CustomType', Type::getType('custom'));
    }

    public function testGetUnknownTypeThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        Type::getType('nonexisting');
    }
}
