<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between (potentially invalid) UTF-16BE and UTF-8.
 */
class Utf16BeEncoding extends AbstractEncodingUtf {
    const BYTE_ORDER_MARK = "\xFE\xFF";
    const ENCODING_NAME   = 'UTF-16BE';
}
