<?php

namespace Instante\Helpers;

use Instante\Utils\StaticClass;

class FileSystem
{
    use StaticClass;
    
    /**
     * Unifies separators, removes ./, xx/.. and multiple separator sequences
     *        
     * @param string file system path
     * @param string output path separator
     * @return string simplified path
     */
    public static function simplifyPath($path, $separator = '/')
    {
        $path = strtr($path, '/\\', "$separator$separator"); // normalize path separators
        $qsep = preg_quote($separator, '~');

        //remove multiple separators
        $path = preg_replace("~$qsep+~", $qsep, $path);

        // remove './' sequences
        $path = preg_replace("~$qsep\\.(?=$qsep|$)~", '', $path);

        // remove 'xxx/../' sequences
        $count = 0;
        do {
            $path = preg_replace("~(?<=^|$qsep)(\\.(?!.)|[^.$qsep])+$qsep\\.\\.(?=$qsep|$)$qsep?~", '', $path, -1, $count);
        } while ($count > 0); // do it repeatedly to eliminate 'xx/yy/../../' sequences
        

        // remove trailing separator
        $path = rtrim($path, $qsep);

        return $path;

    }
}
