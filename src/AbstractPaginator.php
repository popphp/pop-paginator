<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Paginator;

/**
 * Abstract paginator type class
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.5
 */
abstract class AbstractPaginator
{

    /**
     * Total number of items
     * @var int
     */
    protected $total = 0;

    /**
     * Number of items per page
     * @var int
     */
    protected $perPage = 10;

    /**
     * Range of pages per page
     * @var int
     */
    protected $range = 10;

    /**
     * Query key
     * @var string
     */
    protected $queryKey = 'page';

    /**
     * Current page property
     * @var int
     */
    protected $currentPage = 1;

    /**
     * Number of pages property
     * @var int
     */
    protected $numberOfPages = null;

    /**
     * Current page start index property
     * @var int
     */
    protected $start = null;

    /**
     * Current page end index property
     * @var int
     */
    protected $end = null;

    /**
     * Page bookends
     * @var array
     */
    protected $bookends = [
        'start'    => '&laquo;',
        'previous' => '&lsaquo;',
        'next'     => '&rsaquo;',
        'end'      => '&raquo;'
    ];

    /**
     * Constructor
     *
     * Instantiate the paginator object
     *
     * @param  int $total
     * @param  int $perPage
     * @param  int $range
     */
    public function __construct($total, $perPage = 10, $range = 10)
    {
        $this->total   = (int)$total;
        $this->perPage = (int)$perPage;
        $this->range   = (int)$range;
    }

    /**
     * Set the query key
     *
     * @param  string $key
     * @return AbstractPaginator
     */
    public function setQueryKey($key)
    {
        $this->queryKey = $key;
        return $this;
    }

    /**
     * Set the bookends
     *
     * @param  array $bookends
     * @return AbstractPaginator
     */
    public function setBookends(array $bookends)
    {
        if (array_key_exists('start', $bookends)) {
            $this->bookends['start'] = $bookends['start'];
        }
        if (array_key_exists('previous', $bookends)) {
            $this->bookends['previous'] = $bookends['previous'];
        }
        if (array_key_exists('next', $bookends)) {
            $this->bookends['next'] = $bookends['next'];
        }
        if (array_key_exists('end', $bookends)) {
            $this->bookends['end'] = $bookends['end'];
        }

        return $this;
    }

    /**
     * Get the content items total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get the per page
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Get the page range
     *
     * @return int
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Get the query key
     *
     * @return int
     */
    public function getQueryKey()
    {
        return $this->queryKey;
    }

    /**
     * Get the current page
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get the number of pages
     *
     * @return int
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * Get a bookend
     *
     * @param  string $key
     * @return string
     */
    public function getBookend($key)
    {
        return (isset($this->bookends[$key])) ? $this->bookends[$key] : null;
    }

    /**
     * Get the bookends
     *
     * @return array
     */
    public function getBookends()
    {
        return $this->bookends;
    }

    /**
     * Calculate the page range
     *
     * @param  int $page
     * @return array
     */
    public function calculateRange($page = 1)
    {
        $this->currentPage = $page;

        // Calculate the number of pages based on the remainder.
        $remainder = $this->total % $this->perPage;
        $this->numberOfPages = ($remainder != 0) ? (floor(($this->total / $this->perPage)) + 1) :
            floor(($this->total / $this->perPage));

        // Calculate the start index.
        $this->start = ($page * $this->perPage) - $this->perPage;

        // Calculate the end index.
        if (($page == $this->numberOfPages) && ($remainder == 0)) {
            $this->end = $this->start + $this->perPage;
        } else if ($page == $this->numberOfPages) {
            $this->end = (($page * $this->perPage) - ($this->perPage - $remainder));
        } else {
            $this->end = ($page * $this->perPage);
        }

        // Calculate if out of range.
        if ($this->start >= $this->total) {
            $this->start = 0;
            $this->end   = $this->perPage;
        }

        // Check and calculate for any page ranges.
        if (((null === $this->range) || ($this->range > $this->numberOfPages)) && (null === $this->total)) {
            $range = [
                'start' => 1,
                'end'   => $this->numberOfPages,
                'prev'  => false,
                'next'  => false
            ];
        } else {
            // If page is within the first range block.
            if (($page <= $this->range) && ($this->numberOfPages <= $this->range)) {
                $range = [
                    'start' => 1,
                    'end'   => $this->numberOfPages,
                    'prev'  => false,
                    'next'  => false
                ];
            // If page is within the first range block, with a next range.
            } else if (($page <= $this->range) && ($this->numberOfPages > $this->range)) {
                $range = [
                    'start' => 1,
                    'end'   => $this->range,
                    'prev'  => false,
                    'next'  => true
                ];
            // Else, if page is within the last range block, with an uneven remainder.
            } else if ($page > ($this->range * floor($this->numberOfPages / $this->range))) {
                $range = [
                    'start' => ($this->range * floor($this->numberOfPages / $this->range)) + 1,
                    'end'   => $this->numberOfPages,
                    'prev'  => true,
                    'next'  => false
                ];
            // Else, if page is within the last range block, with no remainder.
            } else if ((($this->numberOfPages % $this->range) == 0) && ($page > ($this->range * (($this->numberOfPages / $this->range) - 1)))) {
                $range = [
                    'start' => ($this->range * (($this->numberOfPages / $this->range) - 1)) + 1,
                    'end'   => $this->numberOfPages,
                    'prev'  => true,
                    'next'  => false
                ];
            // Else, if page is within a middle range block.
            } else {
                $posInRange = (($page % $this->range) == 0) ? ($this->range - 1) : (($page % $this->range) - 1);
                $linkStart = $page - $posInRange;
                $range = [
                    'start' => $linkStart,
                    'end'   => $linkStart + ($this->range - 1),
                    'prev'  => true,
                    'next'  => true
                ];
            }
        }

        return $range;
    }

}
