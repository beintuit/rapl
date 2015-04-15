<?php

namespace RAPL\RAPL\Types;

class BooleanType extends Type
{
    /**
     * @param mixed $value
     *
     * @return bool|null
     */
    public function convertToPhpValue($value)
    {
        return ($value === null) ? null : (bool) $value;
    }
}
