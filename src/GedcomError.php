<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

namespace Fisharebest\LibGedcom;

/**
 * Error messages when manipulating GEDCOM data.
 */
class GedcomError {
	const CHARSET_ASSUMED      = 'The character set {0} was assumed.';
	const CHARSET_BOM_UTF8     = 'The GEDCOM file has a UTF-8 byte-order mark and specifies character set {0}.';
	const CHARSET_DETECTED     = 'The character set {0} was detected.';
	const CHARSET_INVALID      = 'The character set "{0}" is invalid.';
	const CHARSET_MISSING      = 'No character set was specified.';
	const CHARSET_TYPE_INVALID = 'The character set/type "{0}/{1}" is invalid.';

	const GEDCOM_LINE_LEADING_ZERO         = 'Line {0} ({1}): The level should not have a leading zero.';
	const GEDCOM_MAX_LINE_LEVEL            = 'Line {0} ({1}): The level should not exceed {2}.';
	const GEDCOM_LINE_SPACE_AFTER_LEVEL    = 'Line {0} ({1}): Incorrect whitespace after the level.';
	const GEDCOM_LINE_SPACE_AFTER_XREF     = 'Line {0} ({1}): Incorrect whitespace after the xref.';
	const GEDCOM_LINE_SPACE_AFTER_TAG      = 'Line {0} ({1}): Incorrect whitespace after the tag.';
	const GEDCOM_LINE_SPACE_AFTER_LINK     = 'Line {0} ({1}): Incorrect whitespace after the link.';
	const GEDCOM_LINE_SPACE_BEFORE_LEVEL   = 'Line {0} ({1}): Incorrect whitespace before the level.';
	const GEDCOM_LINE_XREF_LENGTH          = 'Line {0} ({1}): The xref should not exceed {2} characters.';
	const GEDCOM_LINE_TAG_UPPERCASE        = 'Line {0} ({1}): GEDCOM tags are always uppercase.';
	const GEDCOM_LINE_INVALID              = 'Line {0} ({1}): Not recognised as GEDCOM.';
	const GEDCOM_LINE_SUBSTRUCTURE_CONT    = 'Line {0} ({1}): Substructures on continuation lines are ignored.';
	const GEDCOM_LINE_SUBSTRUCTURE_LEVEL_0 = 'Line {0} ({1}): Substructures cannot occur at level 0.';

}
