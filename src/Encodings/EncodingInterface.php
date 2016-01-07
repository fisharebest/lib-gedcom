<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and another encoding.
 */
interface EncodingInterface {
    /**
     * Convert a string from UTF-8 encoding to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string;

    /**
     * Convert a string from another encoding to UTF-8 encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string;
}
