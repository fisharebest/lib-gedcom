<?php
/**
 * @copyright 2016 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types = 1);

use Fisharebest\LibGedcom\Filters\GedcomToUtf8;

stream_filter_register(GedcomToUtf8::class, GedcomToUtf8::class);