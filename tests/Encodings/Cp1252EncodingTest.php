<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Encodings;

use Fisharebest\LibGedcom\Encodings\Cp1252Encoding;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class Cp1252Encoding.
 */
class Cp1252EncodingTest extends TestCase
{
	const TEST_DATA = [
		"\x00\x01\x02\x03\x04\x05\x06\x07" => "\x00\x01\x02\x03\x04\x05\x06\x07",
		"\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F" => "\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F",
		"\x10\x11\x12\x13\x14\x15\x16\x17" => "\x10\x11\x12\x13\x14\x15\x16\x17",
		"\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F" => "\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F",
		' !"#$%&\''                        => "\x20\x21\x22\x23\x24\x25\x26\x27",
		'()*+,-./'                         => "\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F",
		'01234567'                         => "\x30\x31\x32\x33\x34\x35\x36\x37",
		'89:;<=>?'                         => "\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F",
		'@ABCDEFG'                         => "\x40\x41\x42\x43\x44\x45\x46\x47",
		'HIJKLMNO'                         => "\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F",
		'PQRSTUVW'                         => "\x50\x51\x52\x53\x54\x55\x56\x57",
		'XYZ[\\]^_'                        => "\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F",
		'`abcdefg'                         => "\x60\x61\x62\x63\x64\x65\x66\x67",
		'hijklmno'                         => "\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F",
		'pqrstuvw'                         => "\x70\x71\x72\x73\x74\x75\x76\x77",
		"xyz{|}~\x7F"                      => "\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F",
		'€‚ƒ„…†‡'                          => "\x80\x82\x83\x84\x85\x86\x87",
		'ˆ‰Š‹ŒŽ'                           => "\x88\x89\x8A\x8B\x8C\x8E",
		'‘’“”•–—'                          => "\x91\x92\x93\x94\x95\x96\x97",
		'˜™š›œžŸ'                          => "\x98\x99\x9A\x9B\x9C\x9E\x9F",
		' ¡¢£¤¥¦§'                         => "\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7",
		'¨©ª«¬­®¯'                         => "\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF",
		'°±²³´µ¶·'                         => "\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7",
		'¸¹º»¼½¾¿'                         => "\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF",
		'ÀÁÂÃÄÅÆÇ'                         => "\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7",
		'ÈÉÊËÌÍÎÏ'                         => "\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF",
		'ÐÑÒÓÔÕÖ×'                         => "\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7",
		'ØÙÚÛÜÝÞß'                         => "\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF",
		'àáâãäåæç'                         => "\xE0\xE1\xE2\xE3\xE4\xE5\xE6\xE7",
		'èéêëìíîï'                         => "\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF",
		'ðñòóôõö÷'                         => "\xF0\xF1\xF2\xF3\xF4\xF5\xF6\xF7",
		'øùúûüýþÿ'                         => "\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF",
	];

	const UNPRINTABLE = [
		"\x81\x8D\x8F\x90\x9D",
	];

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Cp1252Encoding<extended>
	 */
	public function testtoUtf8() {
		$encoding = new Cp1252Encoding;

		foreach (self::TEST_DATA as $utf8 => $other) {
			$this->assertSame($utf8, $encoding->toUtf8($other));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Cp1252Encoding<extended>
	 */
	public function testfromUtf8() {
		$encoding = new Cp1252Encoding;

		foreach (self::TEST_DATA as $utf8 => $other) {
			$this->assertSame($other, $encoding->fromUtf8($utf8));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Cp1252Encoding<extended>
	 */
	public function testUnprintable() {
		$encoding = new Cp1252Encoding;

		foreach (self::UNPRINTABLE as $chars) {
			$this->assertSame('', $encoding->toUtf8($chars));
		}
	}
}
