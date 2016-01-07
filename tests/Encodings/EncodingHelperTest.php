<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Encodings;

use Fisharebest\LibGedcom\Encodings\AbstractEncodingUtf;
use Fisharebest\LibGedcom\Encodings\AnselEncoding;
use Fisharebest\LibGedcom\Encodings\AsciiEncoding;
use Fisharebest\LibGedcom\Encodings\Cp1250Encoding;
use Fisharebest\LibGedcom\Encodings\Cp1251Encoding;
use Fisharebest\LibGedcom\Encodings\Cp1252Encoding;
use Fisharebest\LibGedcom\Encodings\Cp437Encoding;
use Fisharebest\LibGedcom\Encodings\Cp850Encoding;
use Fisharebest\LibGedcom\Encodings\EncodingHelper;
use Fisharebest\LibGedcom\Encodings\MacintoshEncoding;
use Fisharebest\LibGedcom\Encodings\Utf8Encoding;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class EncodingHelper.
 */
class EncodingHelperTest extends TestCase
{
	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\EncodingHelper
	 */
	public function testCharacterSetsEncodings() {
		$encoding_helper = new EncodingHelper;
		$generator       = $encoding_helper->characterSetsEncodings();

		$this->assertEquals([['ANSEL'], new AnselEncoding], $generator->current());

		$generator->next();
		$this->assertEquals([['ASCII'], new AsciiEncoding], $generator->current());

		$generator->next();
		$this->assertEquals([['UTF-8', 'UNICODE'], new Utf8Encoding], $generator->current());

		$generator->next();
		$this->assertEquals([['IBMPC', 'IBM', 'IBM-PC', 'OEM'], new Cp437Encoding], $generator->current());

		$generator->next();
		$this->assertEquals([['MSDOS', 'IBM DOS', 'MS-DOS'], new Cp850Encoding], $generator->current());

		$generator->next();
		$this->assertEquals([['WINDOWS-1250'], new Cp1250Encoding], $generator->current());

		$generator->next();
		$this->assertEquals([['WINDOWS-1251'], new Cp1251Encoding], $generator->current());

		$generator->next();
		$this->assertEquals([['ANSI', 'WINDOWS', 'IBM WINDOWS', 'IBM_WINDOWS', 'CP1252', 'ISO-8859-1', 'ISO8859-1', 'ISO8859', 'LATIN1'], new Cp1252Encoding], $generator->current());

		$generator->next();
		$this->assertEquals([['MACINTOSH', 'ASCII/MACOS ROMAN'], new MacintoshEncoding], $generator->current());

		$generator->next();
		$this->assertNull($generator->current());
	}

	/**
	 * @covers \Fisharebest\LibGedcom\Encodings\EncodingHelper
	 */
	public function testUtf16MagicStrings() {
		$encoding_helper = new EncodingHelper;

		foreach ($encoding_helper->utf16MagicStrings() as $key => $value) {
			$this->assertTrue(is_string($key));
			$this->assertInstanceOf(AbstractEncodingUtf::class, $value);
			$convert = $value->toUtf8($key);

			$this->assertRegExp('/^(|0 HEAD)$/', $convert);
		}
	}
}
