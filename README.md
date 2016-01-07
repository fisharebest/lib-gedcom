[![Latest Unstable Version](https://poser.pugx.org/fisharebest/lib-gedcom/v/unstable)](https://packagist.org/packages/fisharebest/lib-gedcom)
[![Build Status](https://travis-ci.org/fisharebest/lib-gedcom.svg?branch=master)](https://travis-ci.org/fisharebest/lib-gedcom)
[![Coverage Status](https://coveralls.io/repos/github/fisharebest/lib-gedcom/badge.svg)](https://coveralls.io/github/fisharebest/lib-gedcom)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b3e806f-ab51-40c2-81fa-ac896c1bcd8a/mini.png)](https://insight.sensiolabs.com/projects/8b3e806f-ab51-40c2-81fa-ac896c1bcd8a)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/lib-gedcom/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/lib-gedcom/?branch=master)
[![License](https://poser.pugx.org/fisharebest/lib-gedcom/license)](https://packagist.org/packages/fisharebest/lib-gedcom)

This package provides utilities for manipulating GEDCOM data.

Installation
============

Use composer.

```
composer require fisharebest/lib-gedcom dev-master
```

Reading GEDCOM files
====================

GEDCOM files are notorious for failing to adhere to the GEDCOM standard.
A stream filter is available to normalize files as they are read.

* convert encoding to UTF8
* remove indentation
* convert line endings to `\n`
* convert FamilyTreeMaker-style GEDCOM tag-names into GEDCOM tags
* merge `CONC` records onto the previous line

```
$input = fopen('php://stdin', 'r');
$output = fopen('php://stdout', 'w');
stream_filter_append($input, \Fisharebest\LibGedcom\Filters\GedcomToUtf8::class);
stream_copy_to_stream($input, $output);
fclose($input);
fclose($output);
```

You can also pass various options to the stream filter.
* ignore the `CHAR` field in the header and use a specific encoding
* send warnings and errors to a PSR-7 logger
```
$options = [
	'input_encoding' => new \FishAreBest\Encodings\AnselEncoding,
	'logger'         => new \Psr\Log\NullLogger,
];
stream_filter_append($input, \Fisharebest\LibGedcom\Filters\GedcomToUtf8::class, null, $options);
```
