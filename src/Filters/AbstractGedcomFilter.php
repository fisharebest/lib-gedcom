<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Filters;

use php_user_filter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Filter a GEDCOM data stream, one record at a time.
 */
abstract class AbstractGedcomFilter extends php_user_filter {
    /** Error messages */
    const CHARSET_ASSUMED  = 'The character set {0} was assumed.';
    const CHARSET_DETECTED = 'The character set {0} was detected.';
    const CHARSET_INVALID  = 'The character set {0} is invalid.';
    const CHARSET_MISSING  = 'No character set was specified.';
    /** @var LoggerInterface Log errors and warnings here. */
    protected $logger;
    /** @var string A buffer so that we can process complete GEDCOM records. */
    private $data;

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

            // While we have complete GEDCOM records, process them.
            while (preg_match('/(.*?[\r\n]\s*)(0.*)/s', $this->data, $match) === 1) {
                list(, $data, $this->data) = $match;

                // Send this record output.
                $data       = $this->filterData($data);
                $bucket_out = stream_bucket_new($this->stream, $data);
                $return     = PSFS_PASS_ON;
                stream_bucket_append($out, $bucket_out);
            }
        }

        // Process the final record.
        if ($closing && $this->data !== '') {
            $data       = $this->filterData($this->data);
            $bucket_out = stream_bucket_new($this->stream, $data);
            $return     = PSFS_PASS_ON;
            stream_bucket_append($out, $bucket_out);
        }

        return $return;
    }

    /**
     * Apply the filters to the data.
     *
     * @param string $data
     *
     * @return string
     */
    abstract protected function filterData(string $data): string;

    /**
     * Initialization.  Available options are:
     *
     * logger   - optional PSR-7 logging of errors and warnings
     */
    public function onCreate() {
        $this->data   = '';
        $this->logger = $this->params['logger'] ?? new NullLogger;
    }
}
