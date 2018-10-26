<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Encodings;

use Fisharebest\LibGedcom\Encodings\Utf16BeEncoding;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class UTF16BeEncoding.
 */
class Utf16BeEncodingTest extends TestCase
{
	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf16BeEncoding<extended>
	 */
	public function testtoUtf16Be() {
		$encoding = new Utf16BeEncoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			$this->assertSame($char, $encoding->toUtf8("\x00" . $char));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf16BeEncoding<extended>
	 */
	public function testfromUtf16BeTrimBom() {
		$encoding = new Utf16BeEncoding;

		$this->assertSame('AB', $encoding->toUtf8("\xFE\xFF\x00A\x00B"));
		$this->assertSame("A\u{FEFF}B", $encoding->toUtf8("\x00A\xFE\xFF\x00B"));
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf16BeEncoding<extended>
	 */
	public function testfromUtf16Be() {
		$encoding = new Utf16BeEncoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			$this->assertSame("\x00" . $char, $encoding->fromUtf8($char));
		}
	}
}
