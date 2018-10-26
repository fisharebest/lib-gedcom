<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Filters;

use Fisharebest\LibGedcom\Filters\FilterSyntax;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests for class FilterSyntax.
 */
class FilterSyntaxTest extends TestCase
{
	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterSyntax<extended>
	 */
	public function testConc() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "1 FOO hello\n2 CONC  world\n";
		$output = "1 FOO hello world\n";

		static::assertSame($output, $this->filterString($input, $logger));
	}

	/**
	 * Pass a string through the filter.
	 *
	 * @param string          $input
	 * @param LoggerInterface $logger
	 *
	 * @return string
	 */
	private function filterString($input, $logger): string {
		$stream = fopen('data://text/plain,' . $input, 'r');
		stream_filter_append($stream, FilterSyntax::class, STREAM_FILTER_READ, ['logger' => $logger]);
		$output = '';
		while (!feof($stream)) {
			$output .= fread($stream, 8192);
		}
		fclose($stream);

		return $output;
	}
}
