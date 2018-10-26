<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Test\Filters;

use Fisharebest\LibGedcom\Filters\FilterEndOfLine;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests for class FilterEndOfLIne.
 */
class FilterEndOfLineTest extends TestCase
{
	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEndOfLine<extended>
	 */
	public function testEolLf() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "\n FOO\nBAR \n BAZ \n";
		$output = "\n FOO\nBAR \n BAZ \n";

		$this->assertSame($output, $this->filterString($input, $logger));
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEndOfLine<extended>
	 */
	public function testEolCr() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "\r FOO\rBAR \r BAZ \r";
		$output = "\n FOO\nBAR \n BAZ \n";

		$this->assertSame($output, $this->filterString($input, $logger));
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEndOfLine<extended>
	 */
	public function testEolCrLf() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "\r\n FOO\r\nBAR \r\n BAZ \r\n";
		$output = "\n FOO\nBAR \n BAZ \n";

		$this->assertSame($output, $this->filterString($input, $logger));
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEndOfLine<extended>
	 */
	public function testEolLfCr() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "\n\r FOO\n\rBAR \n\r BAZ \n\r";
		$output = "\n FOO\nBAR \n BAZ \n";

		$this->assertSame($output, $this->filterString($input, $logger));
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEndOfLine<extended>
	 */
	public function testEolMultiLfCr() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "\n\n\r FOO\n\r\nBAR \r\n\r BAZ \n\r\r";
		$output = "\n FOO\nBAR \n BAZ \n";

		$this->assertSame($output, $this->filterString($input, $logger));
	}

	/**
	 * @covers Fisharebest\LibGedcom\Filters\FilterEndOfLine<extended>
	 */
	public function testBlankLines() {
		$logger = $this->createMock(LoggerInterface::class);

		$input  = "\r \n  \r\n\t \t\n\r";
		$output = "\n \n  \n\t \t\n";

		$this->assertSame($output, $this->filterString($input, $logger));
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
		stream_filter_append($stream, FilterEndOfLine::class, STREAM_FILTER_READ, ['logger' => $logger]);
		$output = '';
		while (!feof($stream)) {
			$output .= fread($stream, 8192);
		}
		fclose($stream);

		return $output;
	}
}
