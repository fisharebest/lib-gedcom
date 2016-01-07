<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and Windows Code Page 850.
 */
class Cp850Encoding extends AbstractEncodingConv implements EncodingInterface {
	public function encodingName() : string {
		return 'CP850';
	}
}
