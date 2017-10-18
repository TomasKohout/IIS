<?php


namespace Instante\Helpers;


use ArrayAccess;
use Exception;
use Throwable; // PHP 7 ready
use Nette\InvalidStateException;

/**
 * Simplifies access to complex nested structure where any member on the path can be null.
 *
 * Usage: instead of <code>$object->getSomething()->property->call()->['array_key'],</code>
 * where anything after getSomething() may be NULL or not exist at all, use:
 * <code>SafeGet::{null|exception}($object)->getSomething()->property['array_key']->_</code>
 *
 * null|exception methods wrap any any variable into SafeGet instance, which prevents triggering
 * errors when something is not set by magic methods.
 *
 * Magic property '_', which can be seen at the end of the chain, simply un-wraps the result
 * (or NULL if started with SafeGet::null and retrieving nested value fails).
 */
final class SafeGet implements ArrayAccess
{
    const FAIL_RETURN_NULL = 1;
    const FAIL_THROW_EXCEPTION = 2;

    /** @var mixed */
    private $wrappedObject;

    /** @var int enumeration of self::FAIL_* */
    private $onFailure;

    private function __construct($wrappedObject, $onFailure)
    {
        $this->wrappedObject = $wrappedObject;
        $this->onFailure = $onFailure;
    }

    /**
     * Creates instance of SafeGet which returns NULL from unwrapping property _
     * when retrieving nested value fails.
     *
     * @param mixed $object
     * @return SafeGet
     */
    public static function null($object)
    {
        return new self($object, self::FAIL_RETURN_NULL);
    }

    /**
     * Creates instance of SafeGet which throws MissingValueException
     * when retrieving nested value fails.
     *
     * @param mixed $object
     * @return SafeGet
     */
    public static function exception($object)
    {
        return new self($object, self::FAIL_THROW_EXCEPTION);
    }

    public function __call($name, $arguments)
    {
        $this->executeSafe(function() use ($name, $arguments) {
            $this->wrappedObject = call_user_func_array([$this->wrappedObject, $name], $arguments);
        }, $name, MissingValueException::ACCESS_METHOD);
        return $this;
    }

    public function __get($name)
    {
        if ($name === '_') {
            return $this->wrappedObject;
        }
        $this->executeSafe(function() use ($name) {
            $this->wrappedObject = $this->wrappedObject->$name;
        }, $name, MissingValueException::ACCESS_PROPERTY);
        return $this;
    }

    private function executeSafe(callable $cb, $identifier, $accessType)
    {
        try {
            set_error_handler(function () use ($identifier, $accessType) {
                $this->fail($identifier, $accessType);
            });
            $cb();
        } catch (MissingValueException $e) {
            throw $e; //don't catch errors caught by error handler
        } catch (Exception $e) {
            $this->fail($identifier, $accessType, $e);
        } catch (Throwable $e) {
            $this->fail($identifier, $accessType);
        } finally {
            restore_error_handler();
        }
    }

    public function __isset($name)
    {
        return isset($this->wrappedObject->$name);
    }


    public function __set($name, $value)
    {
        throw new InvalidStateException('Cannot modify wrapped object inside ' . __CLASS__);
    }


    public function offsetExists($offset)
    {
        return $this->hasArrayAccess() && isset($this->wrappedObject[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->executeSafe(function() use ($offset) {
            $this->wrappedObject = $this->wrappedObject[$offset];
        }, $offset, MissingValueException::ACCESS_ARRAY);
        return $this;
    }

    public function offsetSet($offset, $value)
    {
        throw new InvalidStateException('Cannot modify wrapped object inside ' . __CLASS__);
    }

    public function offsetUnset($offset)
    {
        throw new InvalidStateException('Cannot modify wrapped object inside ' . __CLASS__);
    }

    private function hasArrayAccess()
    {
        return is_array($this->wrappedObject) || $this->wrappedObject instanceof ArrayAccess;
    }

    private function fail($key, $accessType, Exception $previous = NULL)
    {
        switch ($this->onFailure) {
            case self::FAIL_RETURN_NULL:
                $this->wrappedObject = NULL;
                break;
            case self::FAIL_THROW_EXCEPTION:
                throw new MissingValueException($this->wrappedObject, $key, $accessType, $previous);
        }
    }
}
