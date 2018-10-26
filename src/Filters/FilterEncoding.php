<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Filters;

use Fisharebest\LibGedcom\Encodings\AsciiEncoding;
use Fisharebest\LibGedcom\Encodings\EncodingHelper;
use Fisharebest\LibGedcom\Encodings\EncodingInterface;

/**
 * Filter a GEDCOM data stream - convert to UTF8.
 */
class FilterEncoding extends AbstractGedcomFilter {
    /** @var EncodingInterface|null Convert data from this encoding (default = auto-detect) */
    private $encoding;

    /**
     * Initialization.  Available options are:
     *
     * encoding - use this encoding, ignore HEAD/CHAR
     */
    public function onCreate() {
        parent::onCreate();

        $this->encoding = $this->params['encoding'] ?? null;
    }

    /**
     * Apply text filters to the data.
     *
     * @param string $data
     *
     * @return string
     */
    protected function filterData(string $data): string {
        // If we need to auto-detect the encoding, do it from the first record.
        if ($this->encoding === null) {
            $this->encoding = $this->detectEncodingFromHeader($data);
        }

        // The order of these is important.
        $data = $this->encoding->toUtf8($data);
        $data = $this->fixHeaderCharacterSet($data);

        return $data;
    }

    /**
     * @param string $gedcom_record
     *
     * @return EncodingInterface
     */
    private function detectEncodingFromHeader(string $gedcom_record): EncodingInterface {
        $encoding_helper = new EncodingHelper;

        // UTF encodings are unambiguous
        foreach ($encoding_helper->utf16MagicStrings() as $magic_string => $encoding) {
            if (substr_compare($gedcom_record, $magic_string, 0, strlen($magic_string)) === 0) {
                $this->logger->info(self::CHARSET_DETECTED, [$encoding::ENCODING_NAME]);

                return $encoding;
            }
        }

        // Use a very loose interpretation of GEDCOM, as this data is not yet normalized.
        preg_match(
            '/^\s*0+\s*HEAD(?:ER)?[^\r\n]*' .
            '(?:[\r\n]\s*0*[1-9] [^\r\n]*)*' .
            '(?:[\r\n]\s*0*1 CHAR(?:ACTER)? (?P<CHAR>[^\r\n]*))' .
            '(?:[\r\n]\s*0*2 TYPE (?P<TYPE>[^\r\n]*))?' .
            '/', $gedcom_record, $match);
        $char = trim(strtoupper($match['CHAR'] ?? ''));
        $type = trim(strtoupper($match['TYPE'] ?? ''));

        if ($type !== '') {
            $char .= '/' . $type;
        }

        foreach ($encoding_helper->characterSetsEncodings() as $character_sets_encoding) {
            list($character_sets, $encoding) = $character_sets_encoding;
            if (in_array($char, $character_sets)) {
                if ($char === $encoding::ENCODING_NAME) {
                    $this->logger->info(self::CHARSET_DETECTED, [$char]);
                } else {
                    $this->logger->error(self::CHARSET_INVALID, [$char]);
                    $this->logger->notice(self::CHARSET_ASSUMED, [$encoding::ENCODING_NAME]);
                }

                return $encoding;
            }
        }

        if ($char === '') {
            $this->logger->error(self::CHARSET_MISSING);
        } else {
            $this->logger->error(self::CHARSET_INVALID, [$char]);
        }
        $this->logger->notice(self::CHARSET_ASSUMED, [AsciiEncoding::ENCODING_NAME]);

        return new AsciiEncoding;
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
