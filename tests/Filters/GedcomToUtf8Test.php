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
use Fisharebest\LibGedcom\Filters\GedcomToUtf8;
use Fisharebest\LibGedcom\GedcomFile;
use Psr\Log\LoggerInterface;

/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
class GedcomToUtf8Test extends PHPUnit_Framework_TestCase{
	public function setUp() {
		stream_filter_register('gedcom_to_utf8', GedcomToUtf8::class);
	}

	/**
	 * Read a file and pass it through the filter.
	 *
	 * @param string          $filename
	 * @param LoggerInterface $logger
	 */
	private function readFile($filename, $logger) {
		$input = fopen(__DIR__ . '/../data/encodings/' . $filename, 'r');
		stream_filter_append($input, 'gedcom_to_utf8',  STREAM_FILTER_READ, ['logger' => $logger]);
		while(fread($input, 8192)) {}
		fclose($input);
	}

	public function testDetectAscii() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['ASCII']);

		$this->readFile('ASCII.ged', $logger);
	}

	public function testDetectAnsel() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL.ged', $logger);
	}

	public function testDetectAnselBom() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The GEDCOM file has a UTF-8 byte-order mark and specifies character set {0}.', ['ANSEL']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['UTF-8']);

		$this->readFile('ANSEL+BOM.ged', $logger);
	}

	public function testDetectAnselText() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-TEXT.ged', $logger);
	}

	public function testDetectAnselCr() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-CR.ged', $logger);
	}

	public function testDetectAnselCrLf() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-CRLF.ged', $logger);
	}

	public function testDetectAnselLfCr() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['ANSEL']);

		$this->readFile('ANSEL-LFCR.ged', $logger);
	}

	public function testDetectUtf8() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('UTF-8.ged', $logger);
	}

	public function testDetectUnicode() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['UNICODE']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['UTF-8']);

		$this->readFile('UNICODE.ged', $logger);
	}

	public function testDetectUtf8Bom() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['UTF-8']);

		$this->readFile('UTF-8+BOM.ged', $logger);
	}

	public function testDetectUtf16Le() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['UTF-16LE']);

		$this->readFile('UTF-16LE.ged', $logger);
	}

	public function testDetectUtf16LeBom() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['UTF-16LE']);

		$this->readFile('UTF-16LE+BOM.ged', $logger);
	}

	public function testDetectUtf16Be() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['UTF-16BE']);

		$this->readFile('UTF-16BE.ged', $logger);
	}

	public function testDetectUtf16BeBom() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('info')->with('The character set {0} was detected.', ['UTF-16BE']);

		$this->readFile('UTF-16BE+BOM.ged', $logger);
	}

	public function testDetectMacintosh() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['MACINTOSH']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['MACINTOSH']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['MacOS Roman']);

		$this->readFile('MACINTOSH.ged', $logger);
	}

	public function testDetectAsciiMacintosh() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set/type "{0}/{1}" is invalid.', ['ASCII', 'MACOS ROMAN']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['MacOS Roman']);

		$this->readFile('ASCII+MACINTOSH.ged', $logger);
	}

	public function testDetectIbmPc() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['IBMPC']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['IBMPC']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('IBMPC.ged', $logger);
	}

	public function testDetectIbm_Pc() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['IBM-PC']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['IBM-PC']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('IBM-PC.ged', $logger);
	}

	public function testDetectIbm() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['IBM']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['IBM']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('IBM.ged', $logger);
	}

	public function testDetectOem() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['OEM']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['OEM']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 437']);

		$this->readFile('OEM.ged', $logger);
	}

	public function testDetectMsDos() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['MSDOS']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['MSDOS']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 850']);

		$this->readFile('MSDOS.ged', $logger);
	}

	public function testDetectMs_Dos() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['MS-DOS']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['MS-DOS']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 850']);

		$this->readFile('MS-DOS.ged', $logger);
	}

	public function testDetectIbmDos() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['IBM DOS']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['IBM DOS']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 850']);

		$this->readFile('IBM_DOS.ged', $logger);
	}

	public function testDetectWindows1250() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['WINDOWS-1250']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1250']);

		$this->readFile('WINDOWS-1250.ged', $logger);
	}

	public function testDetectWindows1251() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['WINDOWS-1251']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1251']);

		$this->readFile('WINDOWS-1251.ged', $logger);
	}

	public function testDetectAnsi() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['ANSI']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['ANSI']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ANSI.ged', $logger);
	}

	public function testDetectWindows() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['WINDOWS']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['WINDOWS']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('WINDOWS.ged', $logger);
	}

	public function testDetectIbm_Windows() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['IBM_WINDOWS']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['IBM_WINDOWS']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('IBM_WINDOWS.ged', $logger);
	}

	public function testDetectIbmWindows() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['IBM WINDOWS']);
		$logger->shouldReceive('warning')->with('The character set "{0}" is ambiguous.', ['IBM WINDOWS']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('IBMWINDOWS.ged', $logger);
	}

	public function testDetectCp1252() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['CP1252']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('CP1252.ged', $logger);
	}

	public function testDetectIso_8859_1() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['ISO-8859-1']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ISO-8859-1.ged', $logger);
	}

	public function testDetectIso8859_1() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['ISO8859-1']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ISO8859-1.ged', $logger);
	}

	public function testDetectIso8859() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['ISO8859']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('ISO8859.ged', $logger);
	}

	public function testDetectLatin1() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['LATIN1']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['Code Page 1252']);

		$this->readFile('LATIN1.ged', $logger);
	}

	public function testDetectInvalid() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('The character set "{0}" is invalid.', ['INVALID']);
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['ASCII']);

		$this->readFile('INVALID.ged', $logger);
	}

	public function testDetectMissing() {
		$logger = Mockery::mock(LoggerInterface::class);
		$logger->shouldReceive('error')->with('No character set was specified.');
		$logger->shouldReceive('notice')->with('The character set {0} was assumed.', ['ASCII']);

		$this->readFile('MISSING.ged', $logger);
	}
}
