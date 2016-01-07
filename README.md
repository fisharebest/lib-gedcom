[![Latest Unstable Version](https://poser.pugx.org/fisharebest/lib-gedcom/v/unstable)](https://packagist.org/packages/fisharebest/lib-gedcom)
[![Build Status](https://travis-ci.org/fisharebest/lib-gedcom.svg?branch=master)](https://travis-ci.org/fisharebest/lib-gedcom)
[![Coverage Status](https://coveralls.io/repos/github/fisharebest/lib-gedcom/badge.svg)](https://coveralls.io/github/fisharebest/lib-gedcom)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b3e806f-ab51-40c2-81fa-ac896c1bcd8a/mini.png)](https://insight.sensiolabs.com/projects/8b3e806f-ab51-40c2-81fa-ac896c1bcd8a)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/lib-gedcom/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/lib-gedcom/?branch=master)
[![License](https://poser.pugx.org/fisharebest/lib-gedcom/license)](https://packagist.org/packages/fisharebest/lib-gedcom)

This package provides utilities for manipulating GEDCOM data.

# Installation

Use composer - `composer require fisharebest/lib-gedcom`.

# Reading GEDCOM files

GEDCOM files rarely follow the GEDCOM standard, which makes them
difficult to process.  Stream filters are available, which normalize
files.  Just add them to the input stream.

* convert encoding to UTF8
* remove indentation
* convert line endings to `\n`
* convert FamilyTreeMaker-style GEDCOM tag-names into GEDCOM tags
* merge `CONC` records onto the previous line

```php
$stream = fopen('FILE.GED', 'r');
stream_filter_append($stream, \Fisharebest\LibGedcom\Filters\FilterEncoding::class);  // Convert to UTF-8
stream_filter_append($stream, \Fisharebest\LibGedcom\Filters\FilterEndOfLine::class); // Convert to \n
stream_filter_append($stream, \Fisharebest\LibGedcom\Filters\FilterSyntax::class);    // Spaces, etc.
$data = stream_get_contents($stream);
fclose($stream);
```

The streams accept arrays of options.  Unrecognised options are ignored,
so it is no problem to pass the same array to every filter.

```php
$options = [
  // Ignore the character set in the GEDCOM header and convert
  // from this encoding to UTF-8.
  'encoding' => new \FishAreBest\Encodings\AnselEncoding,
  // Write errors and warnings to a PSR-7 compatible logger.
  'logger'   => new \Psr\Log\NullLogger,
];
stream_filter_append($input, Foo::class, null, $options);
stream_filter_append($input, Bar::class, null, $options);
```

