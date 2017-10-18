<?php

namespace Instante\Utils\Nette;

use Nette\MemberAccessException;
use Nette\Utils\ObjectMixin;

trait ObjectEvents
{
    /**
     * Simple gate for Nette object events.
     * @param  string method name
     * @param  array arguments
     * @return mixed
     * @throws MemberAccessException
     */
    public function __call($name, $args)
    {
        if ($name >= 'onA' && $name < 'on_') {
            return ObjectMixin::call($this, $name, $args);
        } else {
            throw new MemberAccessException($name);
        }
    }
}
