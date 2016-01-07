<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and Windows Code Page 1251.
 */
class Cp1251Encoding extends AbstractEncodingConv {
	protected function encodingName() : string {
		return 'Windows-1251';
	}
}
