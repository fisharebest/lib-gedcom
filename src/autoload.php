<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

use Fisharebest\LibGedcom\Filters\FilterEncoding;
use Fisharebest\LibGedcom\Filters\FilterSyntax;

stream_filter_register(FilterEncoding::class, FilterEncoding::class);
stream_filter_register(FilterSyntax::class, FilterSyntax::class);