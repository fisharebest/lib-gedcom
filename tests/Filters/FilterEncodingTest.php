<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Filters;

use Fisharebest\LibGedcom\Filters\FilterEncoding;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests for class FilterEncoding.
 */
class FilterEncodingTest extends TestCase
{
	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAscii() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ASCII']);

		$this->readFile('ASCII.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnsel() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnselBom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('ANSEL+BOM.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnselText() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-TEXT.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnselCr() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-CR.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnselCrLf() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-CRLF.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnselLfCr() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger->expects($this->once())->method('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-LFCR.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUtf8() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('UTF-8.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUnicode() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['UNICODE']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['UTF-8']);

		$this->readFile('UNICODE.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUtf8Bom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('UTF-8+BOM.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUtf16Le() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16LE']);

		$this->readFile('UTF-16LE.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUtf16LeBom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16LE']);

		$this->readFile('UTF-16LE+BOM.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUtf16Be() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16BE']);

		$this->readFile('UTF-16BE.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectUtf16BeBom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16BE']);

		$this->readFile('UTF-16BE+BOM.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectMacintosh() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['MACINTOSH']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['MacOS Roman']);

		$this->readFile('MACINTOSH.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAsciiMacintosh() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['ASCII/MACOS ROMAN']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['MacOS Roman']);

		$this->readFile('ASCII+MACINTOSH.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIbmPc() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['IBMPC']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP437']);

		$this->readFile('IBMPC.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIbm_Pc() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['IBM-PC']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP437']);

		$this->readFile('IBM-PC.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIbm() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['IBM']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP437']);

		$this->readFile('IBM.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectOem() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['OEM']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP437']);

		$this->readFile('OEM.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectMsDos() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['MSDOS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP850']);

		$this->readFile('MSDOS.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectMs_Dos() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['MS-DOS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP850']);

		$this->readFile('MS-DOS.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIbmDos() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['IBM DOS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['CP850']);

		$this->readFile('IBM_DOS.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectWindows1250() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['WINDOWS-1250']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1250']);

		$this->readFile('WINDOWS-1250.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectWindows1251() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['WINDOWS-1251']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1251']);

		$this->readFile('WINDOWS-1251.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectAnsi() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['ANSI']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('ANSI.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectWindows() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['WINDOWS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('WINDOWS.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIbm_Windows() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['IBM_WINDOWS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('IBM_WINDOWS.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIbmWindows() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['IBM WINDOWS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('IBMWINDOWS.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectCp1252() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['CP1252']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('CP1252.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIso_8859_1() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['ISO-8859-1']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('ISO-8859-1.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIso8859_1() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['ISO8859-1']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('ISO8859-1.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectIso8859() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['ISO8859']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('ISO8859.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectLatin1() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['LATIN1']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Windows-1252']);

		$this->readFile('LATIN1.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectInvalid() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set {0} is invalid.', ['INVALID']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['ASCII']);

		$this->readFile('INVALID.ged', $logger);
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEncoding<extended>
	 */
	public function testDetectMissing() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('No character set was specified.');
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['ASCII']);

		$this->readFile('MISSING.ged', $logger);
	}

	/**
	 * Read a file and pass it through the filter.
	 *
	 * @param string          $filename
	 * @param LoggerInterface $logger
	 */
	private function readFile($filename, $logger) {
		$input = fopen(__DIR__ . '/../data/encodings/' . $filename, 'r');
		stream_filter_append($input, FilterEncoding::class, STREAM_FILTER_READ, ['logger' => $logger]);
		while (!feof($input)) {
			fread($input, 8192);
		}
		fclose($input);
	}
}
