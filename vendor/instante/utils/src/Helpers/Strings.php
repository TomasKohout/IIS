<?php

namespace Instante\Helpers;

use Instante\Utils\StaticClass;
use Nette\InvalidArgumentException;

class Strings
{
    use StaticClass;

    public static function afterLast($string, $separator)
    {
        $pos = mb_strrpos($string, $separator);
        return $pos === FALSE ? $string : mb_substr($string, $pos + mb_strlen($separator));
    }

    public static function format($str, array $args)
    {
        $result = '';
        while (preg_match('~^(?P<prefix>(?:[^%]|%%)*)%\((?P<name>[a-z0-9_-]+)(?:;(?P<format>[^)]+))?\)(?P<postfix>.*)$~i', $str, $m)) {
            $str = $m['postfix'];
            $arg = $args[$m['name']];
            $result .= str_replace('%%', '%', $m['prefix'])
                . (!empty($m['format']) ? sprintf('%' . $m['format'], $arg) : $arg);
        }
        $result .= str_replace('%%', '%', $str);

        return $result;
    }
}
