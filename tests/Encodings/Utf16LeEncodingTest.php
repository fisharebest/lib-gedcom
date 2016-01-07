<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Encodings;

use Fisharebest\LibGedcom\Encodings\Utf16LeEncoding;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class UTF16LeEncoding.
 */
class Utf16LeEncodingTest extends TestCase
{
	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf16LeEncoding<extended>
	 */
	public function testtoUtf16Le() {
		$encoding = new Utf16LeEncoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			$this->assertSame($char, $encoding->toUtf8($char . "\x00"));
		}
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf16LeEncoding<extended>
	 */
	public function testfromUtf16LeTrimBom() {
		$encoding = new Utf16LeEncoding;

		$this->assertSame('AB', $encoding->toUtf8("\xFF\xFEA\x00B\x00"));
		$this->assertSame("A\u{FEFF}B", $encoding->toUtf8("A\x00\xFF\xFEB\x00"));
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\Utf16LeEncoding<extended>
	 */
	public function testfromUtf16Le() {
		$encoding = new Utf16LeEncoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			$this->assertSame($char . "\x00", $encoding->fromUtf8($char));
		}
	}
}
