<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Filters;

/**
 * Pass each GEDCOM record to a user-supplied function.
 *
 * Normalize line endings
 */
class FilterCallback extends AbstractGedcomFilter {
    /** @var \Closure A user-supplied function */
    private $callback;

    /**
     * Initialization.  Available options are:
     *
     * logger   - optional PSR-7 logging of errors and warnings
     */
    public function onCreate() {
        $this->callback = $this->params['callback'] ?? function (string $data): string {
                return $data;
            };
    }

    /**
     * Apply the filters to the data.
     *
     * @param string $data
     *
     * @return string
     */
    protected function filterData(string $data): string {
        $this->callback->__invoke($data);

        return $data;
    }
}
