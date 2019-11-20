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
 * Paginator form class
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.5
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
     * Get input separator
     *
     * @return string
     */
    public function getInputSeparator()
    {
        return $this->inputSeparator;
    }

    /**
     * Get the page form string
     *
     * @param  int $page
     * @return string
     */
    public function getFormString($page = null)
    {
        if (null === $page) {
            $page = (isset($_GET[$this->queryKey]) && ((int)$_GET[$this->queryKey] > 0)) ? (int)$_GET[$this->queryKey] : 1;
        }
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
        $form = '';

        // Preserve any passed GET parameters.
        $query  = null;
        $hidden = null;
        $uri    = null;

        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = (!empty($_SERVER['QUERY_STRING'])) ?
                str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) :
                $_SERVER['REQUEST_URI'];

            if (count($_GET) > 0) {
                $get = $_GET;
                if (isset($get[$this->queryKey])) {
                    unset($get[$this->queryKey]);
                }
                $query = '&' . http_build_query($get);
                foreach ($get as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            $hidden .= '<input type="hidden" name="' . $key . '[' . $k . ']" value="' . $v . '" />';
                        }
                    } else {
                        $hidden .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
                    }
                }
            }
        }

        // Calculate page range
        $pageRange = $this->calculateRange($page);

        $form .= '<form class="pop-paginator-form" action="' . $uri . ((null !== $query) ? '?' . substr($query, 1)  : null) .
            '" method="get"><div><input type="text" name="' . $this->queryKey . '" size="2" value="' .
            $this->currentPage . '" /> ' . $this->inputSeparator . ' ' . $this->numberOfPages . '</div>';

        if (null !== $hidden) {
            $form .= '<div>' . $hidden . '</div>';
        }

        $form .= '</form>';

        $startLinks  = null;
        $endLinks    = null;

        for ($i = $pageRange['start']; $i <= $pageRange['end']; $i++) {
            if (($i == $pageRange['start']) && ($pageRange['prev'])) {
                if (null !== $this->bookends['start']) {
                    $startLinks .= "<a href=\"" . $uri . "?" . $this->queryKey . "=1" . "{$query}\">" .
                        $this->bookends['start'] . "</a>";
                }
                if (null !== $this->bookends['previous']) {
                    $startLinks .= "<a href=\"" . $uri . "?" . $this->queryKey . "=" . ($i - 1) . "{$query}\">" .
                        $this->bookends['previous'] . "</a>";
                }
            }

            if (($i == $pageRange['end']) && ($pageRange['next'])) {
                if (null !== $this->bookends['next']) {
                    $endLinks .= "<a href=\"" . $uri . "?" . $this->queryKey . "=" . ($i + 1) . "{$query}\">" .
                        $this->bookends['next'] . "</a>";
                }
                if (null !== $this->bookends['end']) {
                    $endLinks .= "<a href=\"" . $uri . "?" . $this->queryKey . "=" . $this->numberOfPages .
                        "{$query}\">" . $this->bookends['end'] . "</a>";
                }
            }
        }

        $this->form = $startLinks . $form . $endLinks;
    }

    /**
     * Output the rendered page links
     *
     * @return string
     */
    public function __toString()
    {
        if (empty($this->form)) {
            $this->getFormString();
        }
        return $this->form;
    }

}
