<?php

namespace RAPL\Tests\Fixtures\Types;

use RAPL\RAPL\Types\Type;

class CustomType extends Type
{
    /**
     * Returns the name of this type
     *
     * @return string
     */
    public function getName()
    {
        return 'custom';
    }
}
