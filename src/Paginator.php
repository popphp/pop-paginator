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
 * Paginator factory class
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class Paginator
{

    /**
     * Create paginator form object
     *
     * @param  int $total
     * @param  int $perPage
     * @return Form
     */
    public static function createForm(int $total, int $perPage = 10): Form
    {
        return new Form($total, $perPage);
    }

    /**
     * Create paginator form object
     *
     * @param  int $total
     * @param  int $perPage
     * @param  int $range
     * @return Range
     */
    public static function createRange(int $total, int $perPage = 10, int $range = 10): Range
    {
        return new Range($total, $perPage, $range);
    }

}