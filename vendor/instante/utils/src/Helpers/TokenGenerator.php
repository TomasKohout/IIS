<?php

namespace Instante\Helpers;

/**
 * @author Richard Ejem <richard@ejem.cz>
 */
class TokenGenerator
{
    use \Instante\Utils\StaticClass;
    private static $tokenChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-'; //must be 64 chars

    /**
     * Creates string token of desired length using cryptographically secure openssl_random_pseudo_bytes function.
     *
     * @param int $length
     * @return string
     */
    static function generateToken($length = 40)
    {
        $binToken = openssl_random_pseudo_bytes(ceil($length * 4 / 3));
        $token = '';
        for ($i = 0; $i < $length; ++$i) {
            $token .= self::$tokenChars[BitHelper::subBits($binToken, $i * 6, 6)];
        }
        return $token;
    }
}
