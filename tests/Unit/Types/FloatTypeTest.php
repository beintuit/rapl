<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\Type;

class FloatTypeTest extends AbstractTypeTest
{
    protected function setUp()
    {
        $this->type = Type::getType('float');
    }

    public function testFloatConvertsToPhpValue()
    {
        $actual = $this->type->convertToPhpValue('0.1');
        $this->assertInternalType('float', $actual);
        $this->assertSame(0.1, $actual);

        $actual = $this->type->convertToPhpValue('5');
        $this->assertInternalType('float', $actual);
        $this->assertSame(5.0, $actual);

        $actual = $this->type->convertToPhpValue('-8.33');
        $this->assertInternalType('float', $actual);
        $this->assertSame(-8.33, $actual);
    }
}
