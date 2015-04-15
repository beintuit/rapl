<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\Type;

class BooleanTypeTest extends AbstractTypeTest
{
    protected function setUp()
    {
        $this->type = Type::getType('boolean');
    }

    public function testBooleanConvertsToSerializedValue()
    {
        $this->assertEquals(true, $this->type->convertToSerializedValue(true));
    }

    public function testBooleanConvertsToPhpValue()
    {
        $this->assertEquals(true, $this->type->convertToPhpValue(1));
    }
}
