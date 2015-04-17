<?php

namespace RAPL\RAPL\Types;

use RAPL\RAPL\Mapping\MappingException;

abstract class Type
{
    const TYPE_ARRAY = 'array';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATETIME = 'datetime';
    const TYPE_FLOAT = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING = 'string';

    /**
     * @var array
     */
    private static $typesMap = array(
        self::TYPE_ARRAY    => 'RAPL\RAPL\Types\ArrayType',
        self::TYPE_BOOLEAN  => 'RAPL\RAPL\Types\BooleanType',
        self::TYPE_DATETIME => 'RAPL\RAPL\Types\DateTimeType',
        self::TYPE_FLOAT    => 'RAPL\RAPL\Types\FloatType',
        self::TYPE_INTEGER  => 'RAPL\RAPL\Types\IntegerType',
        self::TYPE_STRING   => 'RAPL\RAPL\Types\StringType'
    );

    /**
     * @var Type[]
     */
    private static $typeObjects = array();

    /**
     * Prevents instantiation and forces use of the factory method.
     */
    final private function __construct()
    {
    }

    /**
     * Converts a value from its PHP representation to its serialized representation
     *
     * @param mixed $value The value to convert
     *
     * @return mixed The serialized representation of the value
     */
    public function convertToSerializedValue($value)
    {
        return $value;
    }

    /**
     * Converts a value from its serialized representation (JSON / XML / etc.) to its PHP representation
     *
     * @param mixed $value The value to convert
     *
     * @return mixed The PHP representation of the value
     */
    public function convertToPhpValue($value)
    {
        return $value;
    }

    /**
     * Factory method to create type instances
     *
     * @param string $name
     *
     * @return Type
     *
     * @throws MappingException
     */
    public static function getType($name)
    {
        if (!isset(self::$typeObjects[$name])) {
            if (!isset(self::$typesMap[$name])) {
                throw MappingException::unknownType($name);
            }
            self::$typeObjects[$name] = new self::$typesMap[$name]();
        }

        return self::$typeObjects[$name];
    }

    /**
     * Adds a custom type to the type map
     *
     * @param string $name
     * @param string $className
     */
    public static function addType($name, $className)
    {
        self::$typesMap[$name] = $className;
    }
}
