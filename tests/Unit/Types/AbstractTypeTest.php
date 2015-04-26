<?php

namespace RAPL\Tests\Unit\Types;

use RAPL\RAPL\Types\Type;

abstract class AbstractTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Type
     */
    protected $type;

    /**
     * @return Type
     */
    abstract protected function getInstance();

    protected function setUp()
    {
        $this->type = $this->getInstance();
    }

    public function testNullConvertsToSerializedValue()
    {
        $this->assertNull($this->type->convertToSerializedValue(null));
    }

    public function testNullConvertsToPhpValue()
    {
        $this->assertNull($this->type->convertToPhpValue(null));
    }
}
