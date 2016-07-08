<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom\Filters;

/**
 * Filter a GEDCOM data stream, one record at a time.
 */
abstract class AbstractFilter extends \php_user_filter {
	/** @var string A buffer so that we can process complete GEDCOM records. */
	private $data = '';

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
			while (preg_match('/(.*[\r\n]\s*)(0.*)/s', $this->data, $match) === 1) {
				list(, $data, $this->data) = $match;

				// Send this record output.
				$bucket_out = stream_bucket_new($this->stream, $this->filterData($data));
				if ($bucket_out === false) {
					return PSFS_ERR_FATAL;
				}
				stream_bucket_append($out, $bucket_out);
				$return = PSFS_PASS_ON;
			}
		}

		// Process the final (or partial) record.
		if ($closing && $this->data !== '') {
			$bucket_out = stream_bucket_new($this->stream, $this->filterData($this->data));
			if ($bucket_out === false) {
				return PSFS_ERR_FATAL;
			}
			stream_bucket_append($out, $bucket_out);
			$return = PSFS_PASS_ON;
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
}
