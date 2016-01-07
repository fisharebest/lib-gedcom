<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and Windows Code Page 1252.
 */
class Cp1252Encoding extends AbstractEncodingIconv {
    const ENCODING_NAME = 'Windows-1252';
}
