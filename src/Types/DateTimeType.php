<?php

namespace RAPL\RAPL\Types;

use DateTime;

class DateTimeType extends Type
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Converts a value from its PHP representation to its serialized representation
     *
     * @param DateTime|null $value The value to convert
     *
     * @return string|null The serialized representation of the value
     */
    public function convertToSerializedValue($value)
    {
        if ($value === null) {
            return $value;
        }

        return $value->format(self::DATETIME_FORMAT);
    }

    /**
     * Converts a value from its serialized representation (JSON / XML / etc.) to its PHP representation
     *
     * @param mixed $value The value to convert
     *
     * @return DateTime|null The PHP representation of the value
     */
    public function convertToPhpValue($value)
    {
        if ($value === null) {
            return $value;
        }

        return DateTime::createFromFormat(self::DATETIME_FORMAT, $value);
    }
}
