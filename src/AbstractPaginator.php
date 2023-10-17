<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
abstract class AbstractPaginator implements PaginatorInterface
{

    /**
     * Total number of items
     * @var int
     */
    protected int $total = 0;

    /**
     * Number of items per page
     * @var int
     */
    protected int $perPage = 10;

    /**
     * Range of pages per page
     * @var int
     */
    protected int $range = 10;

    /**
     * Query key
     * @var string
     */
    protected string $queryKey = 'page';

    /**
     * Current page property
     * @var int
     */
    protected int $currentPage = 1;

    /**
     * Number of pages property
     * @var ?int
     */
    protected ?int $numberOfPages = null;

    /**
     * Current page start index property
     * @var ?int
     */
    protected ?int $start = null;

    /**
     * Current page end index property
     * @var ?int
     */
    protected ?int $end = null;

    /**
     * Page bookends
     * @var array
     */
    protected array $bookends = [
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
    public function __construct(int $total, int $perPage = 10, int $range = 10)
    {
        $this->total   = $total;
        $this->perPage = $perPage;
        $this->range   = $range;
    }

    /**
     * Set the query key
     *
     * @param  string $key
     * @return AbstractPaginator
     */
    public function setQueryKey(string $key): AbstractPaginator
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
    public function setBookends(array $bookends): AbstractPaginator
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
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get the per page
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the page range
     *
     * @return int
     */
    public function getRange(): int
    {
        return $this->range;
    }

    /**
     * Get the query key
     *
     * @return string
     */
    public function getQueryKey(): string
    {
        return $this->queryKey;
    }

    /**
     * Get the current page
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get the number of pages
     *
     * @return int
     */
    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    /**
     * Get a bookend
     *
     * @param  string $key
     * @return string|null
     */
    public function getBookend(string $key): string|null
    {
        return $this->bookends[$key] ?? null;
    }

    /**
     * Get the bookends
     *
     * @return array
     */
    public function getBookends(): array
    {
        return $this->bookends;
    }

    /**
     * Calculate the page range
     *
     * @param  int $page
     * @return array
     */
    public function calculateRange(int $page = 1): array
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
        if ((($this->range === null) || ($this->range > $this->numberOfPages)) && ($this->total === null)) {
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
