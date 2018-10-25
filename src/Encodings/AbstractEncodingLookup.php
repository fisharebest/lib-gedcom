<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and other encodings using a lookup table.
 */
abstract class AbstractEncodingLookup implements EncodingInterface {
    /** An associative array of encoded character => utf8 character. */
    const TO_UTF8 = [];

    /**
     * Convert a string from UTF-8 to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string {
        $utf8  = array_flip(static::TO_UTF8);
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chars = array_map(function ($char) use ($utf8) {
            if (\ord($char) < 128) {
                return $char;
            } else {
                return $utf8[$char] ?? '';
            }
        }, $chars);

        return implode('', $chars);
    }

    /**
     * Convert a string from another encoding to UTF-8.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string {
        return strtr($text, static::TO_UTF8);
    }
}
