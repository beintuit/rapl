<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\Type;

class StringTypeTest extends AbstractTypeTest
{
    protected function setUp()
    {
        $this->type = Type::getType('string');
    }

    public function testStringConvertsToSerializedValue()
    {
        $this->assertEquals('Foo bar.', $this->type->convertToSerializedValue('Foo bar.'));
    }

    public function testStringConvertsToPhpValue()
    {
        $this->assertEquals('Foo bar.', $this->type->convertToPhpValue('Foo bar.'));
    }
}
