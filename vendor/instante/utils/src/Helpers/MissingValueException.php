<?php


namespace Instante\Helpers;


use Exception;
use Nette\InvalidArgumentException;

class MissingValueException extends Exception
{
    const ACCESS_ARRAY = 'array';
    const ACCESS_PROPERTY = 'property';
    const ACCESS_METHOD = 'method';

    /** @var mixed */
    private $object;

    /** @var int enumeration of self::ACCESS_* */
    private $accessType;
    /**
     * @var string
     */
    private $key;

    /**
     * MissingValueException constructor.
     * @param mixed $object
     * @param string $key
     * @param int $accessType enumeration of self::ACCESS_
     * @param Exception $previous
     */
    public function __construct($object, $key, $accessType, Exception $previous = NULL)
    {

        switch ($accessType) {
            case self::ACCESS_ARRAY:
                $value = "['$key']";
                break;
            case self::ACCESS_PROPERTY:
                $value = "->$key";
                break;
            case self::ACCESS_METHOD:
                $value = "->$key()";
                break;
            default:
                throw new InvalidArgumentException('Access type must be enumeration of ' . __CLASS__ . '::ACCESS_*');
        }
        parent::__construct("Missing value $value", 0, $previous);
        $this->key = $key;
        $this->object = $object;
        $this->accessType = $accessType;
    }


    /** @return mixed */
    public function getObject()
    {
        return $this->object;
    }


    /** @return int enumeration of self::ACCESS_* */
    public function getAccessType()
    {
        return $this->accessType;
    }


    /** @return string */
    public function getKey()
    {
        return $this->key;
    }


}
