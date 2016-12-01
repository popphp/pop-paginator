pop-paginator
=============

[![Build Status](https://travis-ci.org/popphp/pop-paginator.svg?branch=master)](https://travis-ci.org/popphp/pop-paginator)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-paginator)](http://cc.popphp.org/pop-paginator/)

OVERVIEW
--------
`pop-paginator` is a component for handling pagination for large data sets.
You can set multiple options to control the display of the pages and the links.

`pop-paginator` is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-paginator` using Composer.

    composer require popphp/pop-paginator

BASIC USAGE
-----------

### Basic example of a list

```php
use Pop\Paginator\Range;

$paginator = new Range(42);
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

### Basic example with a large set, using the bookends

```php
use Pop\Paginator\Range;

$paginator = new Range(4512);
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
the previous set, the beginning of the set or the end.

### Using an input field

To have a cleaner way of displaying a large set of pages, you can use an input field
within a form like this:

```php
use Pop\Paginator\Form;
$paginator = new Form(558);
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

### Other options

You can set many options to tailor the paginator's look and functionality:

* Number of items per page
* Range of the page sets
* Separator between the page links
* Classes for the on/off page links
* Bookend characters
    + start
    + previous
    + next
    + end