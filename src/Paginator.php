<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
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
