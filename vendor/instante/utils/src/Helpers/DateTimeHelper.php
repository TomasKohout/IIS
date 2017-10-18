<?php

namespace Instante\Helpers;

use DateTime;

/**
 * Support functions for working with PHP DateTime
 *
 * @author Richard Ejem <richard@ejem.cz>
 */
class DateTimeHelper
{
    use \Instante\Utils\StaticClass;

    public static function parse($string)
    {
        if ($string == '') {
            return NULL;
        }
        $string = str_replace('. ', '.', $string);
        return new DateTime($string);
    }
}
