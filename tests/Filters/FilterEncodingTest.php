<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

use Fisharebest\LibGedcom\Encodings\AnselEncoding;
use Fisharebest\LibGedcom\Encodings\AsciiEncoding;
use Fisharebest\LibGedcom\Encodings\Cp1250Encoding;
use Fisharebest\LibGedcom\Encodings\Cp1251Encoding;
use Fisharebest\LibGedcom\Encodings\Cp1252Encoding;
use Fisharebest\LibGedcom\Encodings\Cp437Encoding;
use Fisharebest\LibGedcom\Encodings\Cp850Encoding;
use Fisharebest\LibGedcom\Encodings\MacintoshEncoding;
use Fisharebest\LibGedcom\Encodings\Utf16BeEncoding;
use Fisharebest\LibGedcom\Encodings\Utf16LeEncoding;
use Fisharebest\LibGedcom\Encodings\Utf8Encoding;
use Fisharebest\LibGedcom\Filters\FilterEncoding;
use Fisharebest\LibGedcom\GedcomFile;
use Psr\Log\LoggerInterface;

/**
 * Tests for class FilterEncoding.
 */
class FilterEncodingTest extends PHPUnit_Framework_TestCase{
	/**
	 * Read a file and pass it through the filter.
	 *
	 * @param string          $filename
	 * @param LoggerInterface $logger
	 */
	private function readFile($filename, $logger) {
		$input = fopen(__DIR__ . '/../data/encodings/' . $filename, 'r');
		stream_filter_append($input, FilterEncoding::class,  STREAM_FILTER_READ, ['logger' => $logger]);
		while(fread($input, 8192)) {}
		fclose($input);
	}

	public function testDetectAscii() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ASCII']);

		$this->readFile('ASCII.ged', $logger);
	}

	public function testDetectAnsel() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL.ged', $logger);
	}

	public function testDetectAnselBom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The GEDCOM file has a UTF-8 byte-order mark and specifies character set {0}.', ['ANSEL']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['UTF-8']);

		$this->readFile('ANSEL+BOM.ged', $logger);
	}

	public function testDetectAnselText() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-TEXT.ged', $logger);
	}

	public function testDetectAnselCr() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-CR.ged', $logger);
	}

	public function testDetectAnselCrLf() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-CRLF.ged', $logger);
	}

	public function testDetectAnselLfCr() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger->expects($this->once())->method('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-LFCR.ged', $logger);
	}

	public function testDetectUtf8() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('UTF-8.ged', $logger);
	}

	public function testDetectUnicode() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['UNICODE']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['UTF-8']);

		$this->readFile('UNICODE.ged', $logger);
	}

	public function testDetectUtf8Bom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('UTF-8+BOM.ged', $logger);
	}

	public function testDetectUtf16Le() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16LE']);

		$this->readFile('UTF-16LE.ged', $logger);
	}

	public function testDetectUtf16LeBom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16LE']);

		$this->readFile('UTF-16LE+BOM.ged', $logger);
	}

	public function testDetectUtf16Be() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16BE']);

		$this->readFile('UTF-16BE.ged', $logger);
	}

	public function testDetectUtf16BeBom() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('info')
			->with('The character set {0} was detected.', ['UTF-16BE']);

		$this->readFile('UTF-16BE+BOM.ged', $logger);
	}

	public function testDetectMacintosh() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['MACINTOSH']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['MacOS Roman']);

		$this->readFile('MACINTOSH.ged', $logger);
	}

	public function testDetectAsciiMacintosh() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set/type "{0}/{1}" is invalid.', ['ASCII', 'MACOS ROMAN']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['MacOS Roman']);

		$this->readFile('ASCII+MACINTOSH.ged', $logger);
	}

	public function testDetectIbmPc() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['IBMPC']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('IBMPC.ged', $logger);
	}

	public function testDetectIbm_Pc() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['IBM-PC']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('IBM-PC.ged', $logger);
	}

	public function testDetectIbm() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['IBM']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('IBM.ged', $logger);
	}

	public function testDetectOem() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['OEM']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('OEM.ged', $logger);
	}

	public function testDetectMsDos() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['MSDOS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 850']);

		$this->readFile('MSDOS.ged', $logger);
	}

	public function testDetectMs_Dos() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['MS-DOS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 850']);

		$this->readFile('MS-DOS.ged', $logger);
	}

	public function testDetectIbmDos() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['IBM DOS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 850']);

		$this->readFile('IBM_DOS.ged', $logger);
	}

	public function testDetectWindows1250() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['WINDOWS-1250']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1250']);

		$this->readFile('WINDOWS-1250.ged', $logger);
	}

	public function testDetectWindows1251() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['WINDOWS-1251']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1251']);

		$this->readFile('WINDOWS-1251.ged', $logger);
	}

	public function testDetectAnsi() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['ANSI']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ANSI.ged', $logger);
	}

	public function testDetectWindows() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger->expects($this->once())->method('error')->with('The character set "{0}" is invalid.', ['WINDOWS']);
		$logger->expects($this->once())->method('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('WINDOWS.ged', $logger);
	}

	public function testDetectIbm_Windows() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['IBM_WINDOWS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('IBM_WINDOWS.ged', $logger);
	}

	public function testDetectIbmWindows() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['IBM WINDOWS']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('IBMWINDOWS.ged', $logger);
	}

	public function testDetectCp1252() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['CP1252']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('CP1252.ged', $logger);
	}

	public function testDetectIso_8859_1() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['ISO-8859-1']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ISO-8859-1.ged', $logger);
	}

	public function testDetectIso8859_1() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['ISO8859-1']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ISO8859-1.ged', $logger);
	}

	public function testDetectIso8859() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['ISO8859']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ISO8859.ged', $logger);
	}

	public function testDetectLatin1() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['LATIN1']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('LATIN1.ged', $logger);
	}

	public function testDetectInvalid() {
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('The character set "{0}" is invalid.', ['INVALID']);
		$logger
			->expects($this->once())
			->method('notice')
			->with('The character set {0} was assumed.', ['ASCII']);

		$this->readFile('INVALID.ged', $logger);
	}

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
}
