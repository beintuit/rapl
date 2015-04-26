<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\StringType;
use RAPL\RAPL\Types\Type;

class StringTypeTest extends AbstractTypeTest
{
    /**
     * @return StringType
     */
    protected function getInstance()
    {
        return Type::getType('string');
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
