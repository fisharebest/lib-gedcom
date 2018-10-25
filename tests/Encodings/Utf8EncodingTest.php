<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Encodings;

use Fisharebest\LibGedcom\Encodings\Utf8Encoding;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class Utf8Encoding.
 */
class Utf8EncodingTest extends TestCase
{
	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf8Encoding<extended>
	 */
	public function testtoUtf8() {
		$encoding = new Utf8Encoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			static::assertSame($char, $encoding->toUtf8($char));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf8Encoding<extended>
	 */
	public function testfromUtf8InvalidByteSequencesAreIgnored() {
		$encoding = new Utf8Encoding;

		static::assertSame('ABCD', $encoding->toUtf8("AB\xFFCD"));
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf8Encoding<extended>
	 */
	public function testfromUtf8TrimBom() {
		$encoding = new Utf8Encoding;

		static::assertSame('ABCD', $encoding->toUtf8("\u{FEFF}ABCD"));
		static::assertSame("AB\u{FEFF}CD", $encoding->toUtf8("AB\u{FEFF}CD"));
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf8Encoding<extended>
	 */
	public function testfromUtf8() {
		$encoding = new Utf8Encoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			static::assertSame($char, $encoding->fromUtf8($char));
		}
	}
}
