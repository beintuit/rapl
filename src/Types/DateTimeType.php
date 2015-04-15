<?php

namespace RAPL\RAPL\Types;

class DateTimeType extends Type
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function convertToSerializedValue($value)
    {
        if ($value === null) {
            return $value;
        }

        return $value->format(self::DATETIME_FORMAT);
    }

    public function convertToPhpValue($value)
    {
        if ($value === null) {
            return $value;
        }

        return \DateTime::createFromFormat(self::DATETIME_FORMAT, $value);
    }
}
