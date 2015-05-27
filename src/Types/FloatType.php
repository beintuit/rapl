<?php

namespace RAPL\RAPL\Types;

class FloatType extends Type
{
    /**
     * Converts a value from its serialized representation (JSON / XML / etc.) to its PHP representation
     *
     * @param mixed $value The value to convert
     *
     * @return float|null The PHP representation of the value
     */
    public function convertToPhpValue($value)
    {
        return ($value === null) ? null : (float) $value;
    }
}
