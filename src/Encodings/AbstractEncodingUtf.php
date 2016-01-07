<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Encodings;

use Normalizer;

/**
 * Convert between another encoding and UTF-8.
 *
 * A concrete implementation should either
 *
 * 1) implement encodingName() to use iconv()
 * 2) implement utfCharacters() to use strtr()
 * 3) implement toUtf8() and fromUtf8()
 */
abstract class AbstractEncodingUtf extends AbstractEncodingConv {
	/**
	 * UTF encodings use a byte-order-mark as an identifying value.
	 *
	 * @return string
	 */
	abstract public function byteOrderMark() : string;

	/**
	 * Convert a string from another encoding to UTF-8.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function toUtf8(string $text) : string {
		$bom = $this->byteOrderMark();

		if (substr_compare($text, $bom, 0, strlen($bom)) === 0) {
			$text = substr($text, strlen($bom));
		}

		$text = parent::toUtf8($text);
		$text = Normalizer::Normalize($text, Normalizer::NFC);

		return $text;
	}
}
