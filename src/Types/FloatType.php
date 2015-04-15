<?php

namespace RAPL\RAPL\Types;

class FloatType extends Type
{
    public function convertToPhpValue($value)
    {
        return ($value === null) ? null : (float) $value;
    }
}
