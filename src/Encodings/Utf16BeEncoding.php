<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between (potentially invalid) UTF-16BE and UTF-8.
 */
class Utf16BeEncoding extends AbstractEncodingUtf {
	public function encodingName() : string {
		return 'UTF-16BE';
	}

	public function byteOrderMark() : string {
		return "\xFE\xFF";
	}
}
