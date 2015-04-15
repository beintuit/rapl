<?php

namespace RAPL\RAPL\Types;

class IntegerType extends Type
{
    public function convertToPhpValue($value)
    {
        return ($value === null) ? null : (int) $value;
    }
}
