<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

use Fisharebest\LibGedcom\Filters\FilterEncoding;

stream_filter_register(FilterEncoding::class, FilterEncoding::class);