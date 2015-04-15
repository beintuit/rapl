<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\Type;

class DateTimeTypeTest extends AbstractTypeTest
{
    protected function setUp()
    {
        $this->type = Type::getType('datetime');
    }

    public function testDateTimeConvertsToSerializedValue()
    {
        $date = new \DateTime('1992-04-15 14:11:12');

        $expected = $date->format('Y-m-d H:i:s');

        $this->assertEquals($expected, $this->type->convertToSerializedValue($date));
    }

    public function testDateTimeConvertsToPhpValue()
    {
        $date = '1992-04-15 14:11:12';

        $actual = $this->type->convertToPhpValue($date);

        $this->assertInstanceOf('DateTime', $actual);
        $this->assertEquals($date, $actual->format('Y-m-d H:i:s'));
    }
}
