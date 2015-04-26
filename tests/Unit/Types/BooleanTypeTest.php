<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\BooleanType;
use RAPL\RAPL\Types\Type;

class BooleanTypeTest extends AbstractTypeTest
{
    /**
     * @return BooleanType
     */
    protected function getInstance()
    {
        return Type::getType('boolean');
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
