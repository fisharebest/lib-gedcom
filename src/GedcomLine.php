<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom;

use Psr\Log\LoggerInterface;

/**
 * Representation of a line of GEDCOM data.
 */
class GedcomLine {
	/** @var int */
	private $level = INF;

	/** @var string */
	private $xref = '';

	/** @var string */
	private $tag = '';

	/** @var string */
	private $value = '';

	/** @var string */
	private $link = '';

	/** Regular expression to match level+xref+tag+value/link in a GEDCOM line. */
	const REGEX_GEDCOM_LINE =
		'/^' .
		'(\s*)' .               // #1 leading space
		'(0*)' .                // #2 leading zeros
		'(\d+)' .               // #3 level
		'(\s*)' .               // #4 space
		'(?:@([^@]+)@(\s*))?' . // #5/6 xref+space
		'([A-Za-z0-9_]+)' .     // #7 tag
		'(\s?)' .               // #8 space
		'(?:' .
		'(\s*@)' .              // #9 space+@
		'([^@#][^@]*)' .        // #10 link
		'(@\s*)' .              // #11 @+space
		'|' .
		'(.*)' .                // #12 value
		')' .
		'$/';
	const REGEX_GEDCOM_LINE_INDENT = 1;
	const REGEX_GEDCOM_LINE_ZEROES = 2;
	const REGEX_GEDCOM_LINE_LEVEL  = 3;
	const REGEX_GEDCOM_LINE_SPACE1 = 4;
	const REGEX_GEDCOM_LINE_XREF   = 5;
	const REGEX_GEDCOM_LINE_SPACE2 = 6;
	const REGEX_GEDCOM_LINE_TAG    = 7;
	const REGEX_GEDCOM_LINE_SPACE3 = 8;
	const REGEX_GEDCOM_LINE_AT_1   = 9;
	const REGEX_GEDCOM_LINE_LINK   = 10;
	const REGEX_GEDCOM_LINE_AT_2   = 11;
	const REGEX_GEDCOM_LINE_VALUE  = 12;

	/** A list of continuation tags (which cannot have sub-structure XREFs) */
	const CONTINUATION_TAGS = ['CONT', 'CONC', 'CONTINUED', 'CONCATENATION'];

	/** GEDCOM levels should not exceed this number */
	const MAXIMUM_LEVEL = 99;

	/** GEDCOM XREFs should not exceed this length */
	const MAXIMUM_LENGTH_XREF = 20;

	/**
	 * Create a GedcomLine object from a line of GEDCOM text.
	 *
	 * @param int             $line_number
	 * @param string          $text
	 * @param LoggerInterface $logger
	 */
	public function __construct(int $line_number, string $text, LoggerInterface $logger) {
		if (preg_match(self::REGEX_GEDCOM_LINE, $text, $match)) {
			// Level
			if ($match[self::REGEX_GEDCOM_LINE_INDENT] !== '') {
				$logger->warning(GedcomError::GEDCOM_LINE_SPACE_BEFORE_LEVEL, [$line_number, $text]);
			}
			if ($match[self::REGEX_GEDCOM_LINE_ZEROES] !== '') {
				$logger->warning(GedcomError::GEDCOM_LINE_LEADING_ZERO, [$line_number, $text]);
			}
			$this->setLevel((int) $match[self::REGEX_GEDCOM_LINE_LEVEL]);
			if ($match[self::REGEX_GEDCOM_LINE_SPACE1] !== ' ') {
				$logger->warning(GedcomError::GEDCOM_LINE_SPACE_AFTER_LEVEL, [$line_number, $text]);
			}
			if ($this->getLevel() > self::MAXIMUM_LEVEL) {
				$logger->warning(GedcomError::GEDCOM_MAX_LINE_LEVEL, [$line_number, $text, self::MAXIMUM_LEVEL]);
			}

			// XREF
			if (!empty($match[self::REGEX_GEDCOM_LINE_XREF])) {
				if (strlen($match[self::REGEX_GEDCOM_LINE_XREF]) > self::MAXIMUM_LENGTH_XREF) {
					$logger->warning(GedcomError::GEDCOM_LINE_XREF_LENGTH, [$line_number, $text, self::MAXIMUM_LENGTH_XREF]);
				}
				$this->setXref($match[self::REGEX_GEDCOM_LINE_XREF]);
				if ($match[self::REGEX_GEDCOM_LINE_SPACE2] !== ' ') {
					$logger->warning(GedcomError::GEDCOM_LINE_SPACE_AFTER_XREF, [$line_number, $text]);
				}
			}

			// TAG
			$tag = strtoupper($match[self::REGEX_GEDCOM_LINE_TAG]);
			if ($tag !== $match[self::REGEX_GEDCOM_LINE_TAG]) {
				$logger->warning(GedcomError::GEDCOM_LINE_TAG_UPPERCASE, [$line_number, $text]);
			}
			$this->setTag($tag);

			// LINK or VALUE
			if (!empty($match[self::REGEX_GEDCOM_LINE_LINK])) {
				if ($match[self::REGEX_GEDCOM_LINE_SPACE3] !== ' ' || $match[self::REGEX_GEDCOM_LINE_AT_1] !== '@') {
					$logger->warning(GedcomError::GEDCOM_LINE_SPACE_AFTER_TAG, [$line_number, $text]);
				}
				if ($match[self::REGEX_GEDCOM_LINE_AT_2] !== '@') {
					$logger->warning(GedcomError::GEDCOM_LINE_SPACE_AFTER_LINK, [$line_number, $text]);
				}
				$this->setLink($match[self::REGEX_GEDCOM_LINE_LINK]);
			} elseif ($match[self::REGEX_GEDCOM_LINE_VALUE] !== '') {
				if ($match[self::REGEX_GEDCOM_LINE_SPACE3] !== ' ') {
					$logger->warning(GedcomError::GEDCOM_LINE_SPACE_AFTER_TAG, [$line_number, $text]);
				}
				$this->setValue($match[self::REGEX_GEDCOM_LINE_VALUE]);
			} else {
				if ($match[self::REGEX_GEDCOM_LINE_SPACE3] !== '') {
					$logger->warning(GedcomError::GEDCOM_LINE_SPACE_AFTER_TAG, [$line_number, $text]);
				}
			}

			// Sub-structures can only be defined at levels greater than zero.
			if (strpos($this->getXref(), '!') !== false && $this->getLevel() === 0) {
				$logger->warning(GedcomError::GEDCOM_LINE_SUBSTRUCTURE_LEVEL_0, [$line_number, $text]);
			}

			// Continuation lines cannot be designated as sub-structures.
			if ($this->getXref() !== '' && in_array($this->getTag(), self::CONTINUATION_TAGS)) {
				$logger->warning(GedcomError::GEDCOM_LINE_SUBSTRUCTURE_CONT, [$line_number, $text]);
				$this->setXref('');
			}
		} else {
			$logger->error(GedcomError::GEDCOM_LINE_INVALID, [$line_number, $text]);
		}
	}

	/**
	 * @return int
	 */
	public function getLevel() : int {
		return (int) $this->level;
	}

	/**
	 * @param int $level
	 *
	 * @return GedcomLine
	 */
	public function setLevel(int $level) : GedcomLine {
		$this->level = $level;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getXref() : string {
		return (string) $this->xref;
	}

	/**
	 * @param string $xref
	 *
	 * @return GedcomLine
	 */
	public function setXref(string $xref) : GedcomLine {
		$this->xref = $xref;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTag() : string {
		return (string) $this->tag;
	}

	/**
	 * @param string $tag
	 *
	 * @return GedcomLine
	 */
	public function setTag(string $tag) : GedcomLine {
		$this->tag = $tag;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue() : string {
		return (string) $this->value;
	}

	/**
	 * @param string $value
	 *
	 * @return GedcomLine
	 */
	public function setValue(string $value) : GedcomLine {
		$this->value = $value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLink() : string {
		return (string) $this->link;
	}

	/**
	 * @param string $link
	 *
	 * @return GedcomLine
	 */
	public function setLink(string $link) : GedcomLine {
		$this->link = $link;

		return $this;
	}
}
