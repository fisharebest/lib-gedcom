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
class FilterEncoding extends \php_user_filter {
	/** GEDCOM tag-names and their corresponding GEDCOM tags */
	const GEDCOM_TAG_NAMES = [
		'ABBREVIATION'        => 'ABBR',
		'ADDRESS'             => 'ADDR',
		'ADDRESS1'            => 'ADR1',
		'ADDRESS2'            => 'ADR2',
		'ADDRESS3'            => 'ADR3',
		'ADOPTION'            => 'ADOP',
		'ADULT_CHRISTENING'   => 'CHRA',
		'AGENCY'              => 'AGNC',
		'ALIAS'               => 'ALIA',
		'ANCESTORS'           => 'ANCE',
		'ANCES_INTEREST'      => 'ANCI',
		'ANNULMENT'           => 'ANUL',
		'ASSOCIATES'          => 'ASSO',
		'AUTHOR'              => 'AUTH',
		'BAPTISM'             => 'BAPM',
		'BAPTISM_LDS'         => 'BAPL',
		'BAR_MITZVAH'         => 'BARM',
		'BAS_MITZVAH'         => 'BASM',
		'BIRTH'               => 'BIRT',
		'BLESSING'            => 'BLES',
		'BURIAL'              => 'BURI',
		'CALL_NUMBER'         => 'CALN',
		'CASTE'               => 'CAST',
		'CAUSE'               => 'CAUS',
		'CENSUS'              => 'CENS',
		'CHANGE'              => 'CHAN',
		'CHARACTER'           => 'CHAR',
		'CHILD'               => 'CHIL',
		'CHILDREN_COUNT'      => 'NCHI',
		'CHRISTENING'         => 'CHR',
		'CONCATENATION'       => 'CONC',
		'CONFIRMATION'        => 'CONF',
		'CONFIRMATION_LDS'    => 'CONL',
		'CONTINUED'           => 'CONT',
		'COPYRIGHT'           => 'COPR',
		'CORPORATE'           => 'CORP',
		'COUNTRY'             => 'CTRY',
		'CREMATION'           => 'CREM',
		'DEATH'               => 'DEAT',
		'_DEATH_OF_SPOUSE'    => '_DETS',
		'_DEGREE'             => '_DEG',
		'DESCENDANTS'         => 'DESC',
		'DESCENDANT_INT'      => 'DESI',
		'DESTINATION'         => 'DEST',
		'DIVORCE'             => 'DIV',
		'DIVORCE_FILED'       => 'DIVF',
		'EDUCATION'           => 'EDUC',
		'EMIGRATION'          => 'EMIG',
		'ENDOWMENT'           => 'ENDL',
		'ENGAGEMENT'          => 'ENGA',
		'EVENT'               => 'EVEN',
		'FACSIMILE'           => 'FAX',
		'FAMILY'              => 'FAM',
		'FAMILY_CHILD'        => 'FAMC',
		'FAMILY_FILE'         => 'FAMF',
		'FAMILY_SPOUSE'       => 'FAMS',
		'FIRST_COMMUNION'     => 'FCOM',
		'_FILE'               => 'FILE',
		'FORMAT'              => 'FORM',
		'GEDCOM'              => 'GEDC',
		'GIVEN_NAME'          => 'GIVN',
		'GRADUATION'          => 'GRAD',
		'HEADER'              => 'HEAD',
		'HUSBAND'             => 'HUSB',
		'IDENT_NUMBER'        => 'IDNO',
		'IMMIGRATION'         => 'IMMI',
		'INDIVIDUAL'          => 'INDI',
		'LANGUAGE'            => 'LANG',
		'LATITUDE'            => 'LATI',
		'LONGITUDE'           => 'LONG',
		'MARRIAGE'            => 'MARR',
		'MARRIAGE_BANN'       => 'MARB',
		'MARRIAGE_COUNT'      => 'NMR',
		'MARRIAGE_CONTRACT'   => 'MARC',
		'MARRIAGE_LICENSE'    => 'MARL',
		'MARRIAGE_SETTLEMENT' => 'MARS',
		'MEDIA'               => 'MEDI',
		'_MEDICAL'            => '_MDCL',
		'_MILITARY_SERVICE'   => '_MILT',
		'NAME_PREFIX'         => 'NPFX',
		'NAME_SUFFIX'         => 'NSFX',
		'NATIONALITY'         => 'NATI',
		'NATURALIZATION'      => 'NATU',
		'NICKNAME'            => 'NICK',
		'OBJECT'              => 'OBJE',
		'OCCUPATION'          => 'OCCU',
		'ORDINANCE'           => 'ORDI',
		'ORDINATION'          => 'ORDN',
		'PEDIGREE'            => 'PEDI',
		'PHONE'               => 'PHON',
		'PHONETIC'            => 'FONE',
		'PHY_DESCRIPTION'     => 'DSCR',
		'PLACE'               => 'PLAC',
		'POSTAL_CODE'         => 'POST',
		'PROBATE'             => 'PROB',
		'PROPERTY'            => 'PROP',
		'PUBLICATION'         => 'PUBL',
		'QUALITY_OF_DATA'     => 'QUAL',
		'REC_FILE_NUMBER'     => 'RFN',
		'REC_ID_NUMBER'       => 'RIN',
		'REFERENCE'           => 'REFN',
		'RELATIONSHIP'        => 'RELA',
		'RELIGION'            => 'RELI',
		'REPOSITORY'          => 'REPO',
		'RESIDENCE'           => 'RESI',
		'RESTRICTION'         => 'RESN',
		'RETIREMENT'          => 'RETI',
		'ROMANIZED'           => 'ROMN',
		'SEALING_CHILD'       => 'SLGC',
		'SEALING_SPOUSE'      => 'SLGS',
		'SOC_SEC_NUMBER'      => 'SSN',
		'SOURCE'              => 'SOUR',
		'STATE'               => 'STAE',
		'STATUS'              => 'STAT',
		'SUBMISSION'          => 'SUBN',
		'SUBMITTER'           => 'SUBM',
		'SURNAME'             => 'SURN',
		'SURN_PREFIX'         => 'SPFX',
		'TEMPLE'              => 'TEMP',
		'TITLE'               => 'TITL',
		'TRAILER'             => 'TRLR',
		'VERSION'             => 'VERS',
		'WEB'                 => 'WWW',
	];

	/** @var string A buffer so that we can process complete GEDCOM records. */
	private $data;

	/** @var EncodingInterface|null Convert data from this encoding (default = auto-detect) */
	private $input_encoding;

	/** @var LoggerInterface Log errors and warnings here. */
	private $logger;

	/**
	 * Filter some data.
	 *
	 * @param resource $in       Read from this input stream
	 * @param resource $out      Write to this output stream
	 * @param int      $consumed Count of bytes processed
	 * @param bool     $closing  Is the input about to end?
	 *
	 * @return int PSFS_PASS_ON / PSFS_FEED_ME / PSFS_ERR_FATAL
	 */
	public function filter($in, $out, &$consumed, $closing): int {
		$return = PSFS_FEED_ME;

		// While input data is available, continue to read it.
		while ($bucket_in = stream_bucket_make_writeable($in)) {
			$this->data .= $bucket_in->data;
			$consumed   += $bucket_in->datalen;

			// While we have complete records, process them.
			while (preg_match('/(.*[\r\n]\s*)(0.*)/s', $this->data, $match) === 1) {
				list(, $data, $this->data) = $match;

				// Send this record output.
				$bucket_out = stream_bucket_new($this->stream, $this->filterData($data));
				stream_bucket_append($out, $bucket_out);
				$return = PSFS_PASS_ON;
			}
		}

		// Process the final (or partial) record.
		if ($closing && $this->data !== '') {
			$bucket_out = stream_bucket_new($this->stream, $this->filterData($this->data));
			stream_bucket_append($out, $bucket_out);
			$return = PSFS_PASS_ON;
		}

		return $return;
	}

	/**
	 * Initialization.  Available options are:
	 *
	 * logger   - optional PSR-7 logging of errors and warnings
	 * encoding - use this encoding, ignore HEAD/CHAR
	 *
	 * @return bool
	 */
	public function onCreate(): bool {
		$this->data           = '';
		$this->logger         = $this->params['logger'] ?? new NullLogger;
		$this->input_encoding = $this->params['input_encoding'] ?? null;

		return true;
	}

	/**
	 * Cleanup.  Nothing to do here.
	 *
	 * @return bool
	 */
	public function onClose(): bool {
		return true;
	}

	/**
	 * Apply text filters to the data.  The actual filtering happens here.
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	private function filterData(string $data): string {
		// If we need to auto-detect the encoding, do it from the first record.
		if ($this->input_encoding === null) {
			$this->input_encoding = $this->detectEncodingFromHeader($data);
		}

		// The order of these is important.
		$data = $this->input_encoding->toUtf8($data);
		$data = $this->fixLineEndings($data);
		$data = $this->fixHeaderCharacterSet($data);
		$data = $this->mergeConc($data);
		$data = $this->fixFtmTagNames($data);

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
	 * FamilyTreeMaker creates files with tag-names instead of tags.
	 *
	 * @param string $gedcom_record
	 *
	 * @return string
	 */
	private function fixFtmTagNames(string $gedcom_record): string {
		return preg_replace_callback('/(\n\d+ )(\w+)/', [$this, 'fixFtmNamesCallback'], $gedcom_record);
	}

	/**
	 * Replace GEDCOM tag-names with GEDCOM tags.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	private function fixFtmNamesCallback(array $matches): string {
		if (array_key_exists($matches[2], self::GEDCOM_TAG_NAMES)) {
			return $matches[1] . self::GEDCOM_TAG_NAMES[$matches[2]];
		} else {
			return $matches[0];
		}
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