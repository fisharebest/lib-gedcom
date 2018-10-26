<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and Windows Code Page 850.
 */
class Cp850Encoding extends AbstractEncodingIconv {
    const ENCODING_NAME = 'CP850';
}
