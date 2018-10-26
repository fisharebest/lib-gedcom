<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between (potentially invalid) UTF-8 and UTF-8.
 */
class Utf8Encoding extends AbstractEncodingUtf {
    const BYTE_ORDER_MARK = "\u{FEFF}";
    const ENCODING_NAME   = 'UTF-8';
}
