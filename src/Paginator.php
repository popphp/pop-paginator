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
 * Paginator factory class
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.5
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
    public static function createForm($total, $perPage = 10)
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
    public static function createRange($total, $perPage = 10, $range = 10)
    {
        return new Range($total, $perPage, $range);
    }

}