<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between (potentially invalid) UTF-16LE and UTF-8.
 */
class Utf16LeEncoding extends AbstractEncodingUtf {
	public function encodingName() : string {
		return 'UTF-16LE';
	}

	public function byteOrderMark() : string {
		return "\xFF\xFE";
	}
}
