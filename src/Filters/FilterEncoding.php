<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Filters;

use Fisharebest\LibGedcom\Encodings\AnselEncoding;
use Fisharebest\LibGedcom\Encodings\AsciiEncoding;
use Fisharebest\LibGedcom\Encodings\Cp1250Encoding;
use Fisharebest\LibGedcom\Encodings\Cp1251Encoding;
use Fisharebest\LibGedcom\Encodings\Cp1252Encoding;
use Fisharebest\LibGedcom\Encodings\Cp437Encoding;
use Fisharebest\LibGedcom\Encodings\Cp850Encoding;
use Fisharebest\LibGedcom\Encodings\EncodingInterface;
use Fisharebest\LibGedcom\Encodings\MacintoshEncoding;
use Fisharebest\LibGedcom\Encodings\Utf16BeEncoding;
use Fisharebest\LibGedcom\Encodings\Utf16LeEncoding;
use Fisharebest\LibGedcom\Encodings\Utf8Encoding;
use Fisharebest\LibGedcom\GedcomError;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Filter a GEDCOM data stream.
 *
 * Convert to UTF8
 * Normalize whitespace
 * Normalize line endings
 * Merge CONC records
 */
class FilterEncoding extends AbstractFilter {
	/** @var EncodingInterface|null Convert data from this encoding (default = auto-detect) */
	private $input_encoding;

	/** @var LoggerInterface Log errors and warnings here. */
	private $logger;

	/**
	 * Initialization.  Available options are:
	 *
	 * logger   - optional PSR-7 logging of errors and warnings
	 * encoding - use this encoding, ignore HEAD/CHAR
	 *
	 * @return bool
	 */
	public function onCreate(): bool {
		$this->logger         = $this->params['logger'] ?? new NullLogger;
		$this->input_encoding = $this->params['input_encoding'] ?? null;

		return true;
	}

	/**
	 * Apply text filters to the data.  The actual filtering happens here.
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	protected function filterData(string $data): string {
		// If we need to auto-detect the encoding, do it from the first record.
		if ($this->input_encoding === null) {
			$this->input_encoding = $this->detectEncodingFromHeader($data);
		}

		// The order of these is important.
		$data = $this->input_encoding->toUtf8($data);
		$data = $this->fixLineEndings($data);
		$data = $this->fixHeaderCharacterSet($data);
		$data = $this->mergeConc($data);

		return $data;
	}

	/**
	 * @param string $gedcom_record
	 * 
	 * @return EncodingInterface
	 */
	private function detectEncodingFromHeader(string $gedcom_record): EncodingInterface {
		$utf8    = new Utf8Encoding;
		$utf16le = new Utf16LeEncoding;
		$utf16be = new Utf16BeEncoding;

		/** @var Utf8Encoding[] $magic_strings */
		$magic_strings = [
			$utf16le->byteOrderMark()    => $utf16le,
			$utf16le->fromUtf8('0 HEAD') => $utf16le,
			$utf16be->byteOrderMark()    => $utf16be,
			$utf16be->fromUtf8('0 HEAD') => $utf16be,
		];

		// 16 bit encodings are unambiguous
		foreach ($magic_strings as $magic_string => $encoding) {
			foreach ([$encoding->byteOrderMark(), $encoding->fromUtf8('0 HEAD')] as $magic_string) {
				if (substr_compare($gedcom_record, $magic_string, 0, strlen($magic_string)) === 0) {
					$this->logger->info(GedcomError::CHARSET_DETECTED, [$encoding->encodingName()]);

					return $encoding;
				}
			}
		}

		// Use a very loose interpretation of GEDCOM, as this data is not yet normalized.
		preg_match(
			'/^(?P<BOM>' . $utf8->byteOrderMark() . ')?' .
			'0 HEAD[^\r\n]*' .
			'(?:[\r\n]\s*0*[1-9] [^\r\n]*)*' .
			'(?:[\r\n]\s*0*1 CHAR(?:ACTER)? (?P<CHAR>[^\r\n]*))' .
			'(?:[\r\n]\s*0*2 TYPE (?P<TYPE>[^\r\n]*))?' .
			'/', $gedcom_record, $match);
		$bom  = $match['BOM'] ?? '';
		$char = trim(strtoupper($match['CHAR'] ?? ''));
		$type = trim(strtoupper($match['TYPE'] ?? ''));

		if ($type !== '') {
			$this->logger->error(GedcomError::CHARSET_TYPE_INVALID, [$char, $type]);

			if ($char === 'ASCII' && $type === 'MACOS ROMAN') { // GEDitCOM
				$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['MacOS Roman']);

				return new MacintoshEncoding;
			}
		}

		if ($bom !== '') {
			if ($char === 'UTF-8') {
				$this->logger->info(GedcomError::CHARSET_DETECTED, [$char]);
			} else {
				$this->logger->error(GedcomError::CHARSET_BOM_UTF8, [$char]);
				$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['UTF-8']);
			}

			return new Utf8Encoding;
		}

		switch ($char) {
		case 'ANSEL':
			$this->logger->info(GedcomError::CHARSET_DETECTED, [$char]);

			return new AnselEncoding;

		case 'ASCII':
			$this->logger->info(GedcomError::CHARSET_DETECTED, [$char]);

			return new AsciiEncoding;

		case 'UTF-8':
			$this->logger->info(GedcomError::CHARSET_DETECTED, [$char]);

			return new Utf8Encoding;

		case 'MACINTOSH':
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['MacOS Roman']);

			return new MacintoshEncoding;

		case 'IBMPC':
		case 'IBM':    // Reunion
		case 'IBM-PC': // Cumberland Family Tree
		case 'OEM':    // Généatique
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['Code Page 437']);

			return new Cp437Encoding;

		case 'MSDOS':
		case 'IBM DOS': // Reunion, EasyTree
		case 'MS-DOS':  // AbrEdit, FTMwin
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['Code Page 850']);

			return new Cp850Encoding;

		case 'WINDOWS-1250': // GenoPro, Rodokmen Pro
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['Code Page 1250']);

			return new Cp1250Encoding;

		case 'WINDOWS-1251': // Rodovid
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['Code Page 1251']);

			return new Cp1251Encoding;

		case 'ANSI':        // ANSI just means a windows code page.  Assume 1252.
		case 'WINDOWS':     // Parentele
		case 'IBM WINDOWS': // EasyTree, Généalogie, Reunion, TribalPages
		case 'IBM_WINDOWS': // EasyTree
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['Code Page 1252']);

			return new Cp1252Encoding;

		case 'CP1252':      // Lifelines
		case 'ISO-8859-1':  // Cumberland Family Tree, Lifelines
		case 'ISO8859-1':   // Scion Genealogist
		case 'ISO8859':     // Genealogica Grafica
		case 'LATIN1':      // GenealogyJ
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['Code Page 1252']);

			return new Cp1252Encoding;

		case 'UNICODE':
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['UTF-8']);

			return new Utf8Encoding;

		case '':
			$this->logger->error(GedcomError::CHARSET_MISSING);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['ASCII']);

			return new AsciiEncoding;

		default:
			$this->logger->error(GedcomError::CHARSET_INVALID, [$char]);
			$this->logger->notice(GedcomError::CHARSET_ASSUMED, ['ASCII']);

			return new AsciiEncoding;
		}
	}
	
	/**
	 * Convert line-endings to unix format, and remove indentation.
	 *
	 * @param string $gedcom_record
	 *
	 * @return string
	 */
	private function fixLineEndings(string $gedcom_record): string {
		return preg_replace('/[\r\n]\s*/', "\n", $gedcom_record);
	}

	/**
	 * Merge concatenation records.
	 *
	 * @param string $gedcom_record
	 *
	 * @return string
	 */
	private function mergeConc(string $gedcom_record): string {
		return preg_replace('/\n\d (?:@[^@]+@ )?CONC ?/', '', $gedcom_record);
	}

	/**
	 * Set the header record to the new encoding
	 *
	 * @param string $gedcom_record
	 *
	 * @return string
	 */
	private function fixHeaderCharacterSet(string $gedcom_record): string {
		if (substr_compare($gedcom_record, '0 HEAD', 0, 6) === 0) {
			return preg_replace('/1 CHAR .*(\n[2-9].*)*/', '1 CHAR UTF8', $gedcom_record);
		} else {
			return $gedcom_record;
		}
	}
}
