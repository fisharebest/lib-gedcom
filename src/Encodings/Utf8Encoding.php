<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between (potentially invalid) UTF-8 and UTF-8.
 */
class Utf8Encoding extends AbstractEncodingUtf {
	public function encodingName() : string {
		return 'UTF-8';
	}

	public function byteOrderMark() : string {
		return "\u{FEFF}";
	}
}
