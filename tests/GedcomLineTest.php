<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

use Fisharebest\LibGedcom\GedcomLine;
use Psr\Log\LoggerInterface;

/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
class GedcomLineTest extends PHPUnit_Framework_TestCase{
	public function testSimpleTag() {
		$text = '1 TAG';

		$logger = $this->createMock(LoggerInterface::class);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testSimpleXref() {
		$text = '0 @ABC@ TAG';

		$logger = $this->createMock(LoggerInterface::class);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(0, $line->getLevel());
		$this->assertSame('ABC', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testSimpleValue() {
		$text = '2 SUBTAG value';

		$logger = $this->createMock(LoggerInterface::class);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(2, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('SUBTAG', $line->getTag());
		$this->assertSame('value', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testSimpleLink() {
		$text = '4 TAG @XYZ@';

		$logger = $this->createMock(LoggerInterface::class);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(4, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('XYZ', $line->getLink());
	}

	public function testNotRecognisedAsGedcom() {
		$text = 'Hello world!';

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('error')
			->with('Line {0} ({1}): Not recognised as GEDCOM.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(0, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testGedcomTagsAreAlwaysUpperCase() {
		$text = '1 Tag';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): GEDCOM tags are always uppercase.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testIncorrectWhitespaceBeforeLevel() {
		$text = ' 1 TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace before the level.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testIncorrectTabsBeforeLevel() {
		$text = "\t1 TAG";

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace before the level.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testTheLevelShouldNotHaveALeadingZero() {
		$text = '01 TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): The level should not have a leading zero.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testTheLevelShouldNotExceedMax() {
		$text = '100 TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): The level should not exceed {2}.', [1234, $text, 99]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(100, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testNoWhitespaceBetweenLevelAndTag() {
		$text = '1TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the level.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testTabBetweenLevelAndTag() {
		$text = "1\tTAG";

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the level.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testNoWhitespaceBetweenLevelAndXref() {
		$text = '1@ABC@ TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the level.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('ABC', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testTabBetweenLevelAndXref() {
		$text = "1\t@ABC@ TAG";

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the level.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('ABC', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testTheXrefShouldNotExceed20Characters() {
		$text = '1 @ABCDEFGHIJKLMNOPQRSTUVWXYZ@ TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): The xref should not exceed {2} characters.', [1234, $text, 20]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testIncorrectWhitespaceAfterTheXref() {
		$text = '1 @ABC@TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the xref.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('ABC', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testTabAfterTheXref() {
		$text = "1 @ABC@\tTAG";

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the xref.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('ABC', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testExtraWhitespaceAfterTheTag() {
		$text = '1 TAG ';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the tag.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testExtraTabAfterTheTag() {
		$text = "1 TAG\t";

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the tag.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testMissingWhitespaceAfterTheTag() {
		$text = '1 TAG@ABC@';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the tag.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('ABC', $line->getLink());
	}

	public function testTabAfterTheTag() {
		$text = "1 TAG\t@ABC@";

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the tag.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('ABC', $line->getLink());
	}

	public function testExtraWhitespaceBeforeTheLink() {
		$text = '1 TAG  @ABC@';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the tag.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('ABC', $line->getLink());
	}

	public function testExtraWhitespaceAfterTheLink() {
		$text = '1 TAG @ABC@ ';

		$logger = $this->createMock(LoggerInterface::class);
		$logger 
			->expects($this->once()) 
			->method('warning')
			->with('Line {0} ({1}): Incorrect whitespace after the link.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('ABC', $line->getLink());
	}

	public function testXrefWithCont() {
		$text = '1 @ABC!2@ CONT foobar';

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('warning')
			->with('Line {0} ({1}): Substructures on continuation lines are ignored.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('CONT', $line->getTag());
		$this->assertSame('foobar', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testXrefWithContinued() {
		$text = '1 @ABC!2@ CONTINUED foobar';

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('warning')
			->with('Line {0} ({1}): Substructures on continuation lines are ignored.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('CONTINUED', $line->getTag());
		$this->assertSame('foobar', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testXrefWithConc() {
		$text = '1 @ABC!2@ CONC foobar';

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('warning')
			->with('Line {0} ({1}): Substructures on continuation lines are ignored.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('CONC', $line->getTag());
		$this->assertSame('foobar', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testXrefWithConcattion() {
		$text = '1 @ABC!2@ CONCATENATION foobar';

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('warning')
			->with('Line {0} ({1}): Substructures on continuation lines are ignored.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(1, $line->getLevel());
		$this->assertSame('', $line->getXref());
		$this->assertSame('CONCATENATION', $line->getTag());
		$this->assertSame('foobar', $line->getValue());
		$this->assertSame('', $line->getLink());
	}

	public function testSubstructureAtLevel0() {
		$text = '0 @ABC!2@ TAG';

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->once())
			->method('warning')
			->with('Line {0} ({1}): Substructures cannot occur at level 0.', [1234, $text]);

		$line = new GedcomLine(1234, $text, $logger);

		$this->assertSame(0, $line->getLevel());
		$this->assertSame('ABC!2', $line->getXref());
		$this->assertSame('TAG', $line->getTag());
		$this->assertSame('', $line->getValue());
		$this->assertSame('', $line->getLink());
	}
}
