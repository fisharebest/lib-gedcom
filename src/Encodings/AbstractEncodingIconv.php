<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and other encodings using iconv().
 */
abstract class AbstractEncodingIconv implements EncodingInterface {
    /** Concrete implementations should define this. */
    const ENCODING_NAME = '';

    /**
     * Convert a string from UTF-8 to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string {
        return iconv('UTF-8', static::ENCODING_NAME . '//IGNORE', $text);
    }

    /**
     * Convert a string from another encoding to UTF-8.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string {
        return iconv(static::ENCODING_NAME, 'UTF-8//IGNORE', $text);
    }
}
