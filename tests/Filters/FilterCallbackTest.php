<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Filters;

use Fisharebest\LibGedcom\Filters\FilterCallback;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * Tests for class FilterCallback.
 */
class FilterCallbackTest extends TestCase
{
	public function inputData() {
		/**
		 * Data for test
		 *
		 * @return string
		 */
		return
			"0 HEAD\n" .
			"1 GEDC\n" .
			"2 VERS 5.5.1\n" .
			"2 FORM LINEAGE-LINKED\n" .
			"1 CHAR UTF-8\n" .
			"0 @I1@ INDI\n" .
			"0 @I2@ INDI\n" .
			"1 SEX F\n" .
			"0 @F1@ FAM\n" .
			"1 HUSB @I1@\n" .
			"1 WIFE @I2@\n" .
			"0 TRLR\n";
	}

	/**
	 * Data for test.
	 *
	 * @return Generator
	 */
	public function outputData() {
		yield "0 HEAD\n1 GEDC\n2 VERS 5.5.1\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8\n";
		yield "0 @I1@ INDI\n";
		yield "0 @I2@ INDI\n1 SEX F\n";
		yield "0 @F1@ FAM\n1 HUSB @I1@\n1 WIFE @I2@\n";
		yield "0 TRLR\n";
	}

	/**
	 * Run a test.
	 */
	public function testUngreedy() {
		$output = $this->outputData();
		$input  = fopen('data://text/plain,' . $this->inputData(), 'r');

		stream_filter_append($input, FilterCallback::class, STREAM_FILTER_READ, [
			'callback' => function (string $data) use ($output): string {
				static::assertSame($data, $output->current());
				$output->next();

				return $data;
			},
		]);
		while (!feof($input)) {
			fread($input, 8192);
		}
		fclose($input);

		$output->next();
		static::assertNull($output->current());
	}
}
