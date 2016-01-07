<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and other encodings using iconv().
 */
abstract class AbstractEncodingConv implements EncodingInterface {
	/**
	 * What is the encoding name, as used by iconv.
	 *
	 * @return string
	 */
	abstract protected function encodingName() : string;

	/**
	 * Convert a string from UTF-8 to another encoding.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function fromUtf8(string $text) : string {
		return iconv('UTF-8', $this->encodingName() . '//IGNORE', $text);
	}

	/**
	 * Convert a string from another encoding to UTF-8.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function toUtf8(string $text) : string {
		return iconv($this->encodingName(), 'UTF-8//IGNORE', $text);
	}
}
