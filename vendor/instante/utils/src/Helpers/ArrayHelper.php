<?php

namespace Instante\Helpers;

use Nette\InvalidArgumentException;
use Traversable;

/**
 * Supplementary functions for handling arrays
 *
 * @author Richard Ejem
 * @package Instante
 */
class ArrayHelper
{

    /**
     * Maps values from a list of objects/assoc arrays into key-value pairs.
     *
     * @param array|Traversable $values
     * @param string $valueId
     * @param string $keyId
     * @return array
     */
    public static function createKeyMap($values, $valueId = 'name', $keyId = 'id')
    {
        $pairs = [];
        foreach ($values as $value) {
            if (is_object($value)) {
                $pairs[$value->{$keyId}] = $value->{$valueId};
            } else {
                $pairs[$value[$keyId]] = $value[$valueId];
            }
        }
        return $pairs;
    }

    /**
     * Fetches values from an associative array into corresponding properties / setters of an object.
     * Example:
     * <code>
     * <?php
     * class X {
     *     public $foo;
     *     private $bar;
     *     public function setBar($bar) { $this->bar = $bar; }
     * }
     * $x = new X;
     * ArrayHelper::fetchValues($x, ['foo'=>'A', 'bar'=>'B']);
     * </code>
     *
     * @param object $object
     * @param array|Traversable $values
     * @throws InvalidArgumentException
     */
    public static function fetchValues($object, $values)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('first argument must be an object.');
        }
        if (!is_array($values) && !$values instanceof Traversable) {
            throw new InvalidArgumentException('second argument must be an array or Traversable.');
        }
        foreach ($values as $key => $val) {
            $setter = 'set' . ucfirst($key);
            if (!property_exists($object, $key) && !method_exists($object, $setter)) {
                throw new InvalidArgumentException("class " . get_class($object)
                    . " does not have property $key");
            }
            if (method_exists($object, $setter)) {
                $object->$setter($val);
            } else {
                $object->$key = $val;
            }
        }
    }

    /**
     * Improved version of PHP array_map function.
     *
     *  - can traverse through array or \Traversable (basically anything iterable by foreach)
     *  - returns array() if second argument is null
     *
     * @param \Traversable|array $traversable
     * @param callable $callback
     * @return array
     */
    public static function traversableMap($traversable, callable $callback)
    {
        if ($traversable === NULL) {
            return NULL;
        }
        if (!$traversable instanceof Traversable && !is_array($traversable)) {
            throw new InvalidArgumentException('Argument must be \Traversable or array');
        }

        $result = [];
        foreach ($traversable as $key => $val) {
            $x = $callback($val, $key);
            $result[$key] = $x;
        }
        return $result;
    }

    public static function translateValues($traversable, $dictionary, $skipMissing = FALSE)
    {
        return self::traversableMap($traversable, function ($value) use ($dictionary, $skipMissing) {
            if (!isset($dictionary[$value])) {
                if ($skipMissing) {
                    return $value;
                } else {
                    throw new MissingValueException($dictionary, $value, MissingValueException::ACCESS_ARRAY);
                }
            }
            return $dictionary[$value];
        });
    }
}
