<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

use Fisharebest\LibGedcom\Encodings\Utf8Encoding;

/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
class Utf8EncodingTest extends TestCase{
	public function testtoUtf8() {
		$encoding = new Utf8Encoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			$this->assertSame($char, $encoding->toUtf8($char));
		}
	}

	public function testfromUtf8InvalidByteSequencesAreIgnored() {
		$encoding = new Utf8Encoding;

		$this->assertSame('ABCD', $encoding->toUtf8("AB\xFFCD"));
	}

	public function testfromUtf8TrimBom() {
		$encoding = new Utf8Encoding;

		$this->assertSame('ABCD', $encoding->toUtf8("\u{FEFF}ABCD"));
		$this->assertSame("AB\u{FEFF}CD", $encoding->toUtf8("AB\u{FEFF}CD"));
	}

	public function testfromUtf8() {
		$encoding = new Utf8Encoding;

		for ($i = 0; $i < 128; ++$i) {
			$char = chr($i);
			$this->assertSame($char, $encoding->fromUtf8($char));
		}
	}
}
