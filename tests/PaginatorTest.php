<?php

namespace Pop\Paginator\Test;

use Pop\Paginator\Paginator;

class PaginatorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $paginator = new Paginator(100);
        $this->assertInstanceOf('Pop\Paginator\Paginator', $paginator);
        $this->assertEquals(100, $paginator->getTotal());
        $this->assertEquals(10, $paginator->getPerPage());
        $this->assertEquals(10, $paginator->getRange());
        $this->assertEquals(1, $paginator->getCurrentPage());
    }

    public function testSetQueryKey()
    {
        $paginator = new Paginator(100);
        $paginator->setQueryKey('p');
        $this->assertEquals('p', $paginator->getQueryKey());
    }

    public function testSetSeparator()
    {
        $paginator = new Paginator(100);
        $paginator->setSeparator(':');
        $this->assertEquals(':', $paginator->getSeparator());
    }

    public function testSetClasses()
    {
        $paginator = new Paginator(100);
        $paginator->setClassOn('link-on');
        $paginator->setClassOff('link-off');
        $this->assertEquals('link-on', $paginator->getClassOn());
        $this->assertEquals('link-off', $paginator->getClassOff());
    }

    public function testSetBookends()
    {
        $paginator = new Paginator(100);
        $paginator->setBookends([
            'start'    => '<<',
            'previous' => '<',
            'next'     => '>',
            'end'      => '>>'
        ]);
        $this->assertEquals('<<', $paginator->getBookend('start'));
        $this->assertEquals('<', $paginator->getBookend('previous'));
        $this->assertEquals('>', $paginator->getBookend('next'));
        $this->assertEquals('>>', $paginator->getBookend('end'));
        $this->assertEquals('<<', $paginator->getBookends()['start']);
    }

    public function testUseInput()
    {
        $paginator = new Paginator(100);
        $paginator->useInput(true);
        $this->assertEquals(1, $paginator->getRange());
    }

    public function testGetLinks()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'page=2';
        $_GET = [
            'var' => 123
        ];
        $paginator = new Paginator(127);
        $paginator->setBookends([
            'start'    => '<<',
            'previous' => '<',
            'next'     => '>',
            'end'      => '>>'
        ]);
        $links = $paginator->getLinks(3);
        $this->assertEquals(13, $paginator->getNumberOfPages());
    }

    public function testGetLinksInput()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'page=2';
        $_GET = [
            'var' => 123
        ];
        $paginator = new Paginator(127);
        $paginator->useInput(true);
        $links = $paginator->getLinks(13);
        $links = $paginator->getLinks(1);
        $links = $paginator->getLinks(2);
        $links = $paginator->getLinks(12);
        $pages = (string)$paginator;
        $this->assertEquals(13, $paginator->getNumberOfPages());
        $this->assertContains('<form', $pages);
    }

}
