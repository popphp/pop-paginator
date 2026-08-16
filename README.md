pop-paginator
=============

[![Build Status](https://github.com/popphp/pop-paginator/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-paginator/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-paginator)](http://cc.popphp.org/pop-paginator/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Determining the Current Page](#determining-the-current-page)
* [Page Range](#page-range)
* [Page Form](#page-form)
* [Options](#options)
* [Getters](#getters)
* [Error Handling](#error-handling)

Overview
--------
`pop-paginator` is a component for handling pagination for large data sets.
You can set multiple options to control the display of the pages and the links.

`pop-paginator` is a component of the [Pop PHP Framework](https://www.popphp.org/).

Install
-------

Install `pop-paginator` using Composer.

    composer require popphp/pop-paginator

Or, require it in your composer.json file

    "require": {
        "popphp/pop-paginator" : "^5.0.0"
    }

[Top](#pop-paginator)

Quickstart
----------

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createRange(42); // Returns a Pop\Paginator\Range object
echo $paginator;
```

Which will produce this HTML:

```html
<span>1</span>
<a href="/?page=2">2</a>
<a href="/?page=3">3</a>
<a href="/?page=4">4</a>
<a href="/?page=5">5</a>
```

And if you clicked on page 3, it would render:

```html
<a href="/?page=1">1</a>
<a href="/?page=2">2</a>
<span>3</span>
<a href="/?page=4">4</a>
<a href="/?page=5">5</a>
```

[Top](#pop-paginator)

Determining the Current Page
-----------------------------

Both `Range` and `Form` need to know which page is "current" to render the right links and mark the
active page. By default, they read it from the query string using the query key (`page` by default):

```php
$_GET['page']
```

along with `$_SERVER['REQUEST_URI']` and `$_SERVER['QUERY_STRING']` to build each link's URL and to
preserve any other query parameters already on the current request. This is why `echo $paginator` "just
works" in a normal web request without passing anything else in — it reflects the page you're currently
on based on the URL.

If you're rendering outside of a typical web request (a test, a CLI script, an async job) or you simply
want to control the current page yourself, pass it explicitly instead of relying on `$_GET`:

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createRange(4512, 10, 10);
echo $paginator->getLinkRange(12); // renders the range for page 12, ignoring $_GET
```

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createForm(558);
echo $paginator->getFormString(14); // renders the form for page 14, ignoring $_GET
```

If the query key isn't `page` (e.g. you're paginating more than one thing on the same page), set it with
`setQueryKey()` — see [Options](#options).

[Top](#pop-paginator)

Page Range
----------

In the above example, a page range object renders a range of page links. With it, you can
set a large number of pages and have it render "bookend" link before and after the range
as the "previous" and "next" links. Pass the total number of items, the per page limit and the
range limit:

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createRange(4512, 10, 10); // Returns a Pop\Paginator\Range object
echo $paginator;
```

If we go to page 12, it would render:

```html
<a href="/?page=1">&laquo;</a>
<a href="/?page=10">&lsaquo;</a>
<a href="/?page=11">11</a>
<span>12</span>
<a href="/?page=13">13</a>
<a href="/?page=14">14</a>
<a href="/?page=15">15</a>
<a href="/?page=16">16</a>
<a href="/?page=17">17</a>
<a href="/?page=18">18</a>
<a href="/?page=19">19</a>
<a href="/?page=20">20</a>
<a href="/?page=21">&rsaquo;</a>
<a href="/?page=452">&raquo;</a>
```

As you can see, it renders the "bookends" to navigate to the next set of pages,
the previous set, the beginning or end of the set.

### Getting the raw links

If you don't want a single rendered HTML string — for example, to loop over the links yourself in a
template — call `getLinkRange()` to get the array of link strings instead of casting to a string:

```php
$paginator = Paginator::createRange(4512, 10, 10);
$links     = $paginator->getLinkRange(12); // array of HTML strings, one per link/bookend

foreach ($links as $link) {
    echo $link . PHP_EOL;
}
```

### Wrapping each link

`wrapLinks()` wraps every generated link (including bookends) in an HTML element of your choosing —
handy for feeding a `<ul>`-based pagination component:

```php
$paginator = Paginator::createRange(4512, 10, 10);
$items     = $paginator->wrapLinks('li', 'page-link-on', 'page-link-off');

echo '<ul>' . implode('', $items) . '</ul>';
```

which wraps the current page's `<span>` in `<li class="page-link-off">...</li>` and every other page's
`<a>` in `<li class="page-link-on">...</li>`.

[Top](#pop-paginator)

Page Form
---------

To have a cleaner way of displaying a large set of pages, you can use the form object,
which renders a input form field.

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createForm(558); // Returns a Pop\Paginator\Form object
echo $paginator;
```

This will produce:

```html
<a href="/?page=1">&laquo;</a>
<a href="/?page=13">&lsaquo;</a>
<form action="/" method="get">
    <div><input type="text" name="page" size="2" value="14" /> of 56</div>
</form>
<a href="/?page=15">&rsaquo;</a>
<a href="/?page=56">&raquo;</a>
```

So instead of a set a links in between the bookends, there is a form input field
that will allow the user to input a specific page to jump to.

The text between the input and the total page count ("of" by default) can be changed with
`setInputSeparator()`:

```php
$paginator = Paginator::createForm(558);
$paginator->setInputSeparator('/');
echo $paginator; // ...<input type="text" name="page" size="2" value="14" /> / 56...
```

As with `Range`, you can get the rendered form as a string directly with `getFormString()` instead of
casting to a string, and pass an explicit page number to override the one read from `$_GET`:

```php
$paginator = Paginator::createForm(558);
$form      = $paginator->getFormString(14);
```

[Top](#pop-paginator)

Options
-------

You can set many options to tailor the paginator object's look and functionality:

* Number of items per page
* Range of the page sets
* Separator between the page links
* Classes for the on/off page links
* Bookend characters
    + start
    + previous
    + next
    + end
* The query key used to read/write the current page

### Separator

`setSeparator()` sets the string used to join page links together when a `Range` object is cast to a
string (it has no effect on `getLinkRange()`'s raw array):

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createRange(42);
$paginator->setSeparator(' | ');
echo $paginator; // <span>1</span> | <a href="/?page=2">2</a> | <a href="/?page=3">3</a> ...
```

### Classes

`setClassOn()` sets the CSS class applied to the linked (non-current) `<a>` page tags, and
`setClassOff()` sets the class applied to the current page's `<span>` tag:

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createRange(42);
$paginator->setClassOn('page-link');
$paginator->setClassOff('page-current');
echo $paginator;
// <span class="page-current">1</span><a class="page-link" href="/?page=2">2</a>...
```

### Bookends

```php
use Pop\Paginator\Form;
$paginator = new Form(558); // Returns a Pop\Paginator\Form object
$paginator->setBookends([
    'start'    => '&laquo;',
    'previous' => '&lsaquo;',
    'next'     => '&rsaquo;',
    'end'      => '&raquo;'
])
```

The `start` is the far left bookend that takes you back to the beginning.
The `previous` is the left bookend that takes you to the previous page set.
The `next` is the right bookend that takes you to the next page set.
The `end` is the far right bookend that takes you all the way to the end.

`setBookends()` only overwrites the keys you pass, so you can change a single bookend without
re-specifying the rest. Passing `null` for a bookend removes it from the output entirely.

### Query Key

By default, the paginator reads and writes the current page using the `page` query string parameter
(`?page=2`). If that collides with something else on the page — or you're paginating more than one
thing at once — change it with `setQueryKey()`:

```php
use Pop\Paginator\Paginator;

$paginator = Paginator::createRange(4512, 10, 10);
$paginator->setQueryKey('p');
echo $paginator; // links now use ?p=2, ?p=3, etc., and read the current page from $_GET['p']
```

[Top](#pop-paginator)

Getters
-------

Every setting above has a matching getter on the paginator object:

```php
$paginator->getTotal();         // int   — total number of items
$paginator->getPerPage();       // int   — items per page
$paginator->getRange();         // int   — page links shown per range block (Range only; always 1 on Form)
$paginator->getQueryKey();      // string
$paginator->getCurrentPage();   // int   — the page last rendered/calculated
$paginator->getNumberOfPages(); // int   — total number of pages, computed from total/perPage
$paginator->getBookend('next'); // string|null — a single bookend value
$paginator->getBookends();      // array — all four bookend values

// Range only
$paginator->getSeparator();     // string
$paginator->getClassOn();       // string
$paginator->getClassOff();      // string

// Form only
$paginator->getInputSeparator(); // string
```

`getNumberOfPages()` and `getCurrentPage()` are available immediately after construction — you don't
need to render the paginator first to inspect them.

[Top](#pop-paginator)

Error Handling
--------------

The constructor validates its arguments and throws a `Pop\Paginator\Exception` if they don't make
sense — a total below zero, or a `perPage`/`range` value below one:

```php
use Pop\Paginator\Paginator;
use Pop\Paginator\Exception;

try {
    $paginator = Paginator::createRange(100, 0); // perPage must be at least 1
} catch (Exception $exception) {
    // handle the invalid pagination config
}
```

[Top](#pop-paginator)
