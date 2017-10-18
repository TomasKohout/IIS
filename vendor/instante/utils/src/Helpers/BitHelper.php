<?php

namespace Instante\Helpers;

use Nette\InvalidArgumentException;

/**
 * Description of BitHelper
 *
 * @author Richard Ejem <richard@ejem.cz>
 */
class BitHelper
{
    use \Instante\Utils\StaticClass;

    /**
     * Extract sequence of bits from string binary data|int to an integer
     * represented by the bits sequence (useful for logical masks)
     *
     * @param string|int $str
     * @param int start bit
     * @param int number of bits; must be lower than PHP bit size of integer (32 or 64 bits)
     * @return int
     * @throws InvalidArgumentException
     */
    public static function subBits($str, $start, $length)
    {
        if ($length > 8 * PHP_INT_SIZE) {
            throw new InvalidArgumentException('bits length cannot be currently bigger than php int bit size');
        }
        if (is_int($str)) {
            $str = self::intToBinaryString($str);
        }
        if ($start < 0 || $length < 0) {
            throw new InvalidArgumentException('start and length must be positive');
        }
        if ($start + $length > strlen($str)*8) {
            throw new InvalidArgumentException('out of range: start + length = ' . ($start + $length) .
                ', argument string bit length is ' . (strlen($str)*8));
        }
        $startByte = (int)($start / 8);
        $startBit = $start % 8;
        $end = $start + $length - 1;
        $endByte = (int)($end / 8);
        $endBit = $end % 8;
        if ($startByte === $endByte) {
            $number = ord($str[$startByte]) >> (7 - $endBit) & ((2 << ($endBit - $startBit)) - 1);
        } else {
            $number = ord($str[$startByte]) & ((2 << (7 - $startBit)) - 1);
            for ($b = $startByte + 1; $b < $endByte; ++$b) {
                $number <<= 8;
                $number |= ord($str[$b]);
            }
            $number <<= $endBit + 1;
            $number |= (ord($str[$endByte]) >> (7 - $endBit)) & ((2 << $endBit) - 1);
        }
        return $number;
    }

    public static function intToBinaryString($number)
    {
        $str = '';
        for ($mask = 0xFF << ((PHP_INT_SIZE - 1) * 8), $i = PHP_INT_SIZE - 1; $i >= 0; $mask >>= 8, $i--) {
            $str .= chr(($number & $mask) >> ($i * 8));
        }
        return $str;
    }
}
