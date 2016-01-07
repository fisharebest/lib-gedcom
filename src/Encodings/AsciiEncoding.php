<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and ASCII encoding.
 */
class AsciiEncoding extends AbstractEncodingConv {
	public function encodingName() : string {
		return 'ASCII';
	}
}
