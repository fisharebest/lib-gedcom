<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom;

use Psr\Log\LoggerInterface;

/**
 * Representation of a line of GEDCOM data.
 */
class GedcomLine {
    /** Error messages */
    const GEDCOM_LINE_INVALID              = 'Line {0} ({1}): Not recognised as GEDCOM.';
    const GEDCOM_LINE_LEADING_ZERO         = 'Line {0} ({1}): The level should not have a leading zero.';
    const GEDCOM_LINE_MAX_LEVEL            = 'Line {0} ({1}): The level should not exceed {2}.';
    const GEDCOM_LINE_SPACE_AFTER_LEVEL    = 'Line {0} ({1}): Incorrect whitespace after the level.';
    const GEDCOM_LINE_SPACE_AFTER_LINK     = 'Line {0} ({1}): Incorrect whitespace after the link.';
    const GEDCOM_LINE_SPACE_AFTER_TAG      = 'Line {0} ({1}): Incorrect whitespace after the tag.';
    const GEDCOM_LINE_SPACE_AFTER_XREF     = 'Line {0} ({1}): Incorrect whitespace after the xref.';
    const GEDCOM_LINE_SPACE_BEFORE_LEVEL   = 'Line {0} ({1}): Incorrect whitespace before the level.';
    const GEDCOM_LINE_SUBSTRUCTURE_CONT    = 'Line {0} ({1}): Substructures on continuation lines are ignored.';
    const GEDCOM_LINE_SUBSTRUCTURE_LEVEL_0 = 'Line {0} ({1}): Substructures cannot occur at level 0.';
    const GEDCOM_LINE_TAG_UPPERCASE        = 'Line {0} ({1}): GEDCOM tags are always uppercase.';
    const GEDCOM_LINE_XREF_LENGTH          = 'Line {0} ({1}): The xref should not exceed {2} characters.';
    /** Regular expression to match level+xref+tag+value/link in a GEDCOM line. */
    const REGEX_GEDCOM_LINE        =
        '/^' .
        // #1 leading space
        '(\s*)' .
        // #2 leading zeros
        '(0*)' .
        // #3 level
        '(\d+)' .
        // #4 space
        '(\s*)' .
        // #5/6 xref+space
        '(?:@([^@]+)@(\s*))?' .
        // #7 tag
        '([A-Za-z0-9_]+)' .
        // #8 space
        '(\s?)' .
        '(?:' .
        // #9 space+@
        '(\s*@)' .
        // #10 link
        '([^@#][^@]*)' .
        // #11 @+space
        '(@\s*)' .
        '|' .
        // #12 value
        '(.*)' .
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
                $logger->warning(self::GEDCOM_LINE_SPACE_BEFORE_LEVEL, [$line_number, $text]);
            }
            if ($match[self::REGEX_GEDCOM_LINE_ZEROES] !== '') {
                $logger->warning(self::GEDCOM_LINE_LEADING_ZERO, [$line_number, $text]);
            }
            $this->setLevel((int) $match[self::REGEX_GEDCOM_LINE_LEVEL]);
            if ($match[self::REGEX_GEDCOM_LINE_SPACE1] !== ' ') {
                $logger->warning(self::GEDCOM_LINE_SPACE_AFTER_LEVEL, [$line_number, $text]);
            }
            if ($this->getLevel() > self::MAXIMUM_LEVEL) {
                $logger->warning(self::GEDCOM_LINE_MAX_LEVEL, [$line_number, $text, self::MAXIMUM_LEVEL]);
            }

            // XREF
            if (!empty($match[self::REGEX_GEDCOM_LINE_XREF])) {
                if (strlen($match[self::REGEX_GEDCOM_LINE_XREF]) > self::MAXIMUM_LENGTH_XREF) {
                    $logger->warning(self::GEDCOM_LINE_XREF_LENGTH, [$line_number, $text, self::MAXIMUM_LENGTH_XREF]);
                }
                $this->setXref($match[self::REGEX_GEDCOM_LINE_XREF]);
                if ($match[self::REGEX_GEDCOM_LINE_SPACE2] !== ' ') {
                    $logger->warning(self::GEDCOM_LINE_SPACE_AFTER_XREF, [$line_number, $text]);
                }
            }

            // TAG
            $tag = strtoupper($match[self::REGEX_GEDCOM_LINE_TAG]);
            if ($tag !== $match[self::REGEX_GEDCOM_LINE_TAG]) {
                $logger->warning(self::GEDCOM_LINE_TAG_UPPERCASE, [$line_number, $text]);
            }
            $this->setTag($tag);

            // LINK or VALUE
            if (!empty($match[self::REGEX_GEDCOM_LINE_LINK])) {
                if ($match[self::REGEX_GEDCOM_LINE_SPACE3] !== ' ' || $match[self::REGEX_GEDCOM_LINE_AT_1] !== '@') {
                    $logger->warning(self::GEDCOM_LINE_SPACE_AFTER_TAG, [$line_number, $text]);
                }
                if ($match[self::REGEX_GEDCOM_LINE_AT_2] !== '@') {
                    $logger->warning(self::GEDCOM_LINE_SPACE_AFTER_LINK, [$line_number, $text]);
                }
                $this->setLink($match[self::REGEX_GEDCOM_LINE_LINK]);
            } elseif ($match[self::REGEX_GEDCOM_LINE_VALUE] !== '') {
                if ($match[self::REGEX_GEDCOM_LINE_SPACE3] !== ' ') {
                    $logger->warning(self::GEDCOM_LINE_SPACE_AFTER_TAG, [$line_number, $text]);
                }
                $this->setValue($match[self::REGEX_GEDCOM_LINE_VALUE]);
            } else {
                if ($match[self::REGEX_GEDCOM_LINE_SPACE3] !== '') {
                    $logger->warning(self::GEDCOM_LINE_SPACE_AFTER_TAG, [$line_number, $text]);
                }
            }

            // Sub-structures can only be defined at levels greater than zero.
            if ($this->getLevel() === 0 && strpos($this->getXref(), '!') !== false) {
                $logger->warning(self::GEDCOM_LINE_SUBSTRUCTURE_LEVEL_0, [$line_number, $text]);
            }

            // Continuation lines cannot be designated as sub-structures.
            if ($this->getXref() !== '' && in_array($this->getTag(), self::CONTINUATION_TAGS)) {
                $logger->warning(self::GEDCOM_LINE_SUBSTRUCTURE_CONT, [$line_number, $text]);
                $this->setXref('');
            }
        } else {
            $logger->error(self::GEDCOM_LINE_INVALID, [$line_number, $text]);
        }
    }

    /**
     * @return int
     */
    public function getLevel(): int {
        return (int) $this->level;
    }

    /**
     * @param int $level
     *
     * @return GedcomLine
     */
    public function setLevel(int $level): GedcomLine {
        $this->level = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getXref(): string {
        return (string) $this->xref;
    }

    /**
     * @param string $xref
     *
     * @return GedcomLine
     */
    public function setXref(string $xref): GedcomLine {
        $this->xref = $xref;

        return $this;
    }

    /**
     * @return string
     */
    public function getTag(): string {
        return (string) $this->tag;
    }

    /**
     * @param string $tag
     *
     * @return GedcomLine
     */
    public function setTag(string $tag): GedcomLine {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string {
        return (string) $this->value;
    }

    /**
     * @param string $value
     *
     * @return GedcomLine
     */
    public function setValue(string $value): GedcomLine {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string {
        return (string) $this->link;
    }

    /**
     * @param string $link
     *
     * @return GedcomLine
     */
    public function setLink(string $link): GedcomLine {
        $this->link = $link;

        return $this;
    }
}
