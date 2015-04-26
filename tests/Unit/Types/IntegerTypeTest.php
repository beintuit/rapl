<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\IntegerType;
use RAPL\RAPL\Types\Type;

class IntegerTypeTest extends AbstractTypeTest
{
    /**
     * @return IntegerType
     */
    protected function getInstance()
    {
        return Type::getType('integer');
    }

    public function testIntegerConvertsToPhpValue()
    {
        $this->assertInternalType('integer', $this->type->convertToPhpValue('0'));
        $this->assertInternalType('integer', $this->type->convertToPhpValue('1'));
    }
}
