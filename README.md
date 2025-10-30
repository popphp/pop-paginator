pop-paginator
=============

[![Build Status](https://github.com/popphp/pop-paginator/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-paginator/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-paginator)](http://cc.popphp.org/pop-paginator/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Page Range](#page-range)
* [Page Form](#page-form)
* [Options](#options)

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
        "popphp/pop-paginator" : "^4.0.3"
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

[Top](#pop-paginator)

Page Form
---------

To have a cleaner way of displaying a large set of pages, you can use the form object,
which renders a input form field.

```php
use Pop\Paginator\Form;
$paginator = new Form(558); // Returns a Pop\Paginator\Form object
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

[Top](#pop-paginator)
