<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

use Normalizer;

/**
 * Convert between another encoding and UTF-8.
 *
 * A concrete implementation should either:
 *
 * 1) use iconv()
 * 2) implement utfCharacters() to use strtr()
 * 3) implement toUtf8() and fromUtf8()
 */
abstract class AbstractEncodingUtf extends AbstractEncodingIconv {
    /**
     * UTF encodings use a byte-order-mark as an identifying value.
     * Concrete implementations should define this.
     */
    const BYTE_ORDER_MARK = '';

    /**
     * Convert a string from another encoding to UTF-8.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string {
        $bom_length = strlen(static::BYTE_ORDER_MARK);
        if (substr_compare($text, static::BYTE_ORDER_MARK, 0, $bom_length) === 0) {
            $text = substr($text, $bom_length);
        }

        $text = parent::toUtf8($text);
        $text = Normalizer::normalize($text, Normalizer::NFC);

        return $text;
    }
}
