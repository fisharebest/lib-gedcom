<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and Windows Code Page 1252.
 */
class Cp1252Encoding extends AbstractEncodingConv {
	protected function encodingName() : string {
		return 'Windows-1252';
	}
}
