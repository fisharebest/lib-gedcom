<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between (potentially invalid) UTF-16LE and UTF-8.
 */
class Utf16LeEncoding extends AbstractEncodingUtf {
    const BYTE_ORDER_MARK = "\xFF\xFE";
    const ENCODING_NAME   = 'UTF-16LE';
}
