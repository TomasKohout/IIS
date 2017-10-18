<?php

namespace Instante\Utils;

/**
 * Trait for creating constructor placeholders for protected variables.
 *
 * IMPORTANT: class using this trait must be descendant of \Nette\Object,
 * having also loaded nette/reflection package.
 * No check is performed because of performance and
 * a possible collision of trait constructors.
 *
 * Warning: this class is advised to be used ONLY for scaffolding or
 * simple concept purposes. This is just pragmatical to shorten source code,
 * not very clean. Explicitly calling getters and setters ($x->getFoo()
 * rather than magic $x->foo) is advised for clean code, which also gives you
 * better type hints in IDEs.
 *
 * Usage: place @getter and/or @setter annotation to a property docblock
 * to make a protected property readable/writable (if no getXxx/setXxx is defined,
 * this trait provides a default one).

 */
trait AutoGetSet
{
    public function &__get($name)
    {
        try {
            return parent::__get($name);
        } catch (\Nette\MemberAccessException $ex) {
            /* @var $r \Nette\Reflection\ClassType */
            $r = $this->getReflection();
            if (!$r->hasProperty($name)) {
                throw $ex;
            }
            if (!$r->getProperty($name)->hasAnnotation('getter')) {
                throw $ex;
            }

            return $this->$name;
        }
    }

    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (\Nette\MemberAccessException $ex) {
            /* @var $r \Nette\Reflection\ClassType */
            $r = $this->getReflection();
            if (!$r->hasProperty($name)) {
                throw $ex;
            }
            if (!$r->getProperty($name)->hasAnnotation('setter')) {
                throw $ex;
            }
            $this->$name = $value;
        }
    }
}
