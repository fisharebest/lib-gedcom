<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Encodings;

use Fisharebest\LibGedcom\Encodings\AnselEncoding;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class AnselEncoding.
 */
class AnselEncodingTest extends TestCase
{
	const TEST_DATA = [
		"\x00\x01\x02\x03\x04\x05\x06\x07"         => "\x00\x01\x02\x03\x04\x05\x06\x07",
		"\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F"         => "\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F",
		"\x10\x11\x12\x13\x14\x15\x16\x17"         => "\x10\x11\x12\x13\x14\x15\x16\x17",
		"\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F"         => "\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F",
		' !"#$%&\''                                => "\x20\x21\x22\x23\x24\x25\x26\x27",
		'()*+,-./'                                 => "\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F",
		'01234567'                                 => "\x30\x31\x32\x33\x34\x35\x36\x37",
		'89:;<=>?'                                 => "\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F",
		'@ABCDEFG'                                 => "\x40\x41\x42\x43\x44\x45\x46\x47",
		'HIJKLMNO'                                 => "\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F",
		'PQRSTUVW'                                 => "\x50\x51\x52\x53\x54\x55\x56\x57",
		'XYZ[\\]^_'                                => "\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F",
		'`abcdefg'                                 => "\x60\x61\x62\x63\x64\x65\x66\x67",
		'hijklmno'                                 => "\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F",
		'pqrstuvw'                                 => "\x70\x71\x72\x73\x74\x75\x76\x77",
		"xyz{|}~\x7F"                              => "\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F",
		"\xC2\x98\xC2\x9C\xE2\x80\x8D\xE2\x80\x8C" => "\x88\x89\x8D\x8E",
		'ŁØĐÞÆŒʹ'                                  => "\xA1\xA2\xA3\xA4\xA5\xA6\xA7",
		'·♭®±ƠƯʼ'                                  => "\xA8\xA9\xAA\xAB\xAC\xAD\xAE",
		'ʻłøđþæœʺ'                                 => "\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7",
		'ı£ðơư'                                    => "\xB8\xB9\xBA\xBC\xBD",
		'°ℓ℗©♯¿¡ß'                                 => "\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7",
		// Combining diacritics
		'ảàáâãāăȧ'                                 => "\xE0a\xE1a\xE2a\xE3a\xE4a\xE5a\xE6a\xE7a",
		'äǎåa͡a̕a̋a̐'                              => "\xE8a\xE9a\xEAa\xEBa\xEDa\xEEa\xEFa",
		'a̧ąạa̤ḁa̳a̲a̦'                            => "\xF0a\xF1a\xF2a\xF3a\xF4a\xF5a\xF6a\xF7a",
		'a̜a̮a͠a̓a̸'                               => "\xF8a\xF9a\xFAa\xFEa\xFFa",
		// Diacritics with non-ascii
		'ǣ'                                        => "\xE5\xB5",
		// LATIN CAPITAL LETTER O WITH DIAERESIS AND MACRON
		'Ō̈'                                       => "\xE5\xE8O",
		// LATIN CAPITAL LETTER O WITH MACRON AND DIAERESIS
		'Ȫ'                                        => "\xE8\xE5O",
	];

	const UNPRINTABLE = [
		"\x80\x81\x82\x83\x84\x85\x86\x87",
		"\x8A\x8B\x8C\x8F",
		"\x90\x91\x92\x93\x94\x95\x96\x97",
		"\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F",
		"\xA0",
		"\xAF",
		"\xBB\xBE\xBF",
		"\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF",
		"\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7",
		"\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF",
		"\xEC",
		"\xFB\xFC\xFD",
	];

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\AnselEncoding<extended>
	 */
	public function testtoUtf8() {
		$encoding = new AnselEncoding;

		foreach (self::TEST_DATA as $utf8 => $other) {
			static::assertSame($utf8, $encoding->toUtf8($other));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\AnselEncoding<extended>
	 */
	public function testfromUtf8() {
		$encoding = new AnselEncoding;

		foreach (self::TEST_DATA as $utf8 => $other) {
			static::assertSame($other, $encoding->fromUtf8($utf8));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\AnselEncoding<extended>
	 */
	public function testUnprintable() {
		$encoding = new AnselEncoding;

		foreach (self::UNPRINTABLE as $chars) {
			static::assertSame('', $encoding->toUtf8($chars));
		}
	}
}
