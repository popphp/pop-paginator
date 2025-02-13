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
 * Paginator range of links class
 *
 * @category   Pop
 * @package    Pop\Paginator
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.0.2
 */
class Range extends AbstractPaginator
{

    /**
     * Link separator
     * @var ?string
     */
    protected ?string $separator = null;

    /**
     * Page links property
     * @var array
     */
    protected array $links = [];

    /**
     * Class 'on' name for page link tags
     * @var ?string
     */
    protected ?string $classOn = null;

    /**
     * Class 'off' name for page link tags
     * @var ?string
     */
    protected ?string $classOff = null;

    /**
     * Set the bookend separator
     *
     * @param  string $sep
     * @return Range
     */
    public function setSeparator(string $sep): Range
    {
        $this->separator = $sep;
        return $this;
    }

    /**
     * Set the class 'on' name
     *
     * @param  string $class
     * @return Range
     */
    public function setClassOn(string $class): Range
    {
        $this->classOn = $class;
        return $this;
    }

    /**
     * Set the class 'off' name.
     *
     * @param  string $class
     * @return Range
     */
    public function setClassOff(string $class): Range
    {
        $this->classOff = $class;
        return $this;
    }

    /**
     * Get the bookend separator
     *
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Get the page links
     *
     * @param  ?int $page
     * @return array
     */
    public function getLinkRange(?int $page = null): array
    {
        if ($page === null) {
            $page = (isset($_GET[$this->queryKey]) && ((int)$_GET[$this->queryKey] > 0)) ? (int)$_GET[$this->queryKey] : 1;
        }
        $this->calculateRange($page);
        $this->createRange($page);

        return $this->links;
    }

    /**
     * Get the class 'on' name
     *
     * @return string
     */
    public function getClassOn(): string
    {
        return $this->classOn;
    }

    /**
     * Get the class 'off' name.
     *
     * @return string
     */
    public function getClassOff(): string
    {
        return $this->classOff;
    }

    /**
     * Create links
     *
     * @param  int  $page
     * @return void
     */
    public function createRange(int $page = 1): void
    {
        $this->currentPage = $page;

        // Generate the page links.
        $this->links = [];

        // Preserve any passed GET parameters.
        $query = null;
        $uri   = null;

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
            }
        }

        // Calculate page range
        $pageRange = $this->calculateRange($page);

        for ($i = $pageRange['start']; $i <= $pageRange['end']; $i++) {
            $newLink  = null;
            $prevLink = null;
            $nextLink = null;
            $classOff = ($this->classOff !== null) ? " class=\"{$this->classOff}\"" : null;
            $classOn  = ($this->classOn !== null) ? " class=\"{$this->classOn}\"" : null;

            $newLink = ($i == $page) ? "<span{$classOff}>{$i}</span>" : "<a{$classOn} href=\"" . $uri . "?" .
                $this->queryKey . "={$i}{$query}\">{$i}</a>";

            if (($i == $pageRange['start']) && ($pageRange['prev'])) {
                if ($this->bookends['start'] !== null) {
                    $startLink = "<a{$classOn} href=\"" . $uri . "?" . $this->queryKey . "=1" . "{$query}\">" .
                        $this->bookends['start'] . "</a>";
                    $this->links[] = $startLink;
                }
                if ($this->bookends['previous'] !== null) {
                    $prevLink  = "<a{$classOn} href=\"" . $uri . "?" . $this->queryKey . "=" . ($i - 1) . "{$query}\">" .
                        $this->bookends['previous'] . "</a>";
                    $this->links[] = $prevLink;
                }
            }

            $this->links[] = $newLink;

            if (($i == $pageRange['end']) && ($pageRange['next'])) {
                if ($this->bookends['next'] !== null) {
                    $nextLink = "<a{$classOn} href=\"" . $uri . "?" . $this->queryKey . "=" . ($i + 1) . "{$query}\">" .
                        $this->bookends['next'] . "</a>";
                    $this->links[] = $nextLink;
                }
                if ($this->bookends['end'] !== null) {
                    $endLink  = "<a{$classOn} href=\"" . $uri . "?" . $this->queryKey . "=" . $this->numberOfPages .
                        "{$query}\">" . $this->bookends['end'] . "</a>";
                    $this->links[] = $endLink;
                }
            }
        }
    }

    /**
     * Wrap page links in an HTML node
     *
     * @param  string  $node
     * @param  ?string $classOn
     * @param  ?string $classOff
     * @return array
     */
    public function wrapLinks(string $node, ?string $classOn = null, ?string $classOff = null): array
    {
        if (empty($this->links)) {
            $this->getLinkRange();
        }
        $classOff = ($classOff !== null) ? " class=\"{$classOff}\"" : null;
        $classOn  = ($classOn !== null) ? " class=\"{$classOn}\"" : null;

        foreach ($this->links as $i => $link) {
            $this->links[$i] = '<' . $node . ((str_contains($link, 'span')) ? $classOff : $classOn) . '>' . $link . '</' . $node . '>';
        }

        return $this->links;
    }

    /**
     * Output the rendered page links
     *
     * @return string
     */
    public function __toString(): string
    {
        if (empty($this->links)) {
            $this->getLinkRange();
        }
        return implode((string)$this->separator, $this->links) . PHP_EOL;
    }

}
