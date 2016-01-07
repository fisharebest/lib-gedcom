<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Filters;

/**
 * Filter a GEDCOM data stream.
 *
 * Normalize line endings
 */
class FilterEndOfLine extends AbstractGedcomFilter {
    const DEFAULT_EOL = "\n";

    /** @var string Convert line endings to this string */
    private $eol;

    /**
     * Initialization.  Available options are:
     *
     * eol - use this string to represent end of line
     */
    public function onCreate() {
        parent::onCreate();

        $this->eol = $this->params['eol'] ?? self::DEFAULT_EOL;
    }

    /**
     * Apply text filters to the data.
     *
     * @param string $data
     *
     * @return string
     */
    protected function filterData(string $data): string {
        $data = preg_replace('/[\r\n]+/', $this->eol, $data);

        return $data;
    }
}
