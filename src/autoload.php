<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

use Fisharebest\LibGedcom\Filters\FilterCallback;
use Fisharebest\LibGedcom\Filters\FilterEncoding;
use Fisharebest\LibGedcom\Filters\FilterEndOfLine;
use Fisharebest\LibGedcom\Filters\FilterSyntax;

stream_filter_register(FilterCallback::class, FilterCallback::class);
stream_filter_register(FilterEncoding::class, FilterEncoding::class);
stream_filter_register(FilterEndOfLine::class, FilterEndOfLine::class);
stream_filter_register(FilterSyntax::class, FilterSyntax::class);
