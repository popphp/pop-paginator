<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Paginator;

/**
 * Paginator form class
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Form extends AbstractPaginator
{

    /**
     * Input separator
     * @var string
     */
    protected $inputSeparator = 'of';

    /**
     * Page form property
     * @var string
     */
    protected $form = null;

    /**
     * Constructor
     *
     * Instantiate the paginator object
     *
     * @param  int $total
     * @param  int $perPage
     */
    public function __construct($total, $perPage = 10)
    {
        parent::__construct($total, $perPage, 1);
    }

    /**
     * Set input separator
     *
     * @param  string $separator
     * @return Form
     */
    public function setInputSeparator($separator)
    {
        $this->inputSeparator = $separator;
        return $this;
    }

    /**
     * Get the page form string
     *
     * @param  int $page
     * @return string
     */
    public function getFormString($page = 1)
    {
        $this->calculateRange($page);
        $this->createForm($page);

        return $this->form;
    }

    /**
     * Create links
     *
     * @param  int  $page
     * @return void
     */
    protected function createForm($page = 1)
    {
        $this->currentPage = $page;

        // Generate the page links.
        $this->form = '';

        // Preserve any passed GET parameters.
        $query  = null;
        $hidden = null;
        $uri    = null;

        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = (!empty($_SERVER['QUERY_STRING'])) ?
                str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) :
                $_SERVER['REQUEST_URI'];

            if (count($_GET) > 0) {
                foreach ($_GET as $key => $value) {
                    if ($key != $this->queryKey) {
                        $query  .= '&' . $key . '=' . $value;
                        $hidden .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
                    }
                }
            }
        }

        // Calculate page range
        $this->calculateRange($page);

        $this->form = '<form class="pop-paginator-form" action="' . $uri . ((null !== $query) ? '?' . substr($query, 1)  : null) .
            '" method="get"><div><input type="text" name="' . $this->queryKey . '" size="2" value="' .
            $this->currentPage . '" /> ' . $this->inputSeparator . ' ' . $this->numberOfPages . '</div>';

        if (null !== $hidden) {
            $this->form .= '<div>' . $hidden . '</div>';
        }

        $this->form .= '</form>';
    }

    /**
     * Output the rendered page links
     *
     * @return string
     */
    public function __toString()
    {
        $page = (isset($_GET[$this->queryKey]) && ((int)$_GET[$this->queryKey] > 0)) ? (int)$_GET[$this->queryKey] : 1;
        return $this->getFormString($page);
    }

}
