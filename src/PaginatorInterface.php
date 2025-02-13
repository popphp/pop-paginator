<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Paginator;

/**
 * Paginator interface
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.0.2
 */
interface PaginatorInterface
{

    /**
     * Set the query key
     *
     * @param  string $key
     * @return PaginatorInterface
     */
    public function setQueryKey(string $key): PaginatorInterface;

    /**
     * Set the bookends
     *
     * @param  array $bookends
     * @return PaginatorInterface
     */
    public function setBookends(array $bookends): PaginatorInterface;

    /**
     * Get the content items total
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Get the per page
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Get the page range
     *
     * @return int
     */
    public function getRange(): int;

    /**
     * Get the query key
     *
     * @return string
     */
    public function getQueryKey(): string;

    /**
     * Get the current page
     *
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * Get the number of pages
     *
     * @return int
     */
    public function getNumberOfPages(): int;

    /**
     * Get a bookend
     *
     * @param  string $key
     * @return string|null
     */
    public function getBookend(string $key): string|null;

    /**
     * Get the bookends
     *
     * @return array
     */
    public function getBookends(): array;

    /**
     * Calculate the page range
     *
     * @param  int $page
     * @return array
     */
    public function calculateRange(int $page = 1): array;

}
