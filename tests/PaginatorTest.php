<?php

namespace Pop\Paginator\Test;

use Pop\Paginator\Paginator;

class PaginatorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $range = Paginator::createRange(100);
        $form  = Paginator::createForm(100);
        $this->assertInstanceOf('Pop\Paginator\Range', $range);
        $this->assertInstanceOf('Pop\Paginator\Form', $form);
        $this->assertEquals(100, $range->getTotal());
        $this->assertEquals(10, $range->getPerPage());
        $this->assertEquals(10, $range->getRange());
        $this->assertEquals(1, $range->getCurrentPage());
        $this->assertEquals(100, $form->getTotal());
        $this->assertEquals(10, $form->getPerPage());
        $this->assertEquals(1, $form->getRange());
        $this->assertEquals(1, $form->getCurrentPage());
    }

    public function testSetQueryKey()
    {
        $paginator = Paginator::createRange(100);
        $paginator->setQueryKey('p');
        $this->assertEquals('p', $paginator->getQueryKey());
    }

    public function testSetSeparator()
    {
        $paginator = Paginator::createRange(100);
        $paginator->setSeparator(':');
        $this->assertEquals(':', $paginator->getSeparator());
    }

    public function testSetClasses()
    {
        $paginator = Paginator::createRange(100);
        $paginator->setClassOn('link-on');
        $paginator->setClassOff('link-off');
        $this->assertEquals('link-on', $paginator->getClassOn());
        $this->assertEquals('link-off', $paginator->getClassOff());
    }

    public function testSetBookends()
    {
        $paginator = Paginator::createRange(100);
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

    public function testGetRange()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'page=2';
        $_GET = [
            'var' => 123
        ];
        $paginator = Paginator::createRange(127);
        $paginator->setBookends([
            'start'    => '<<',
            'previous' => '<',
            'next'     => '>',
            'end'      => '>>'
        ]);
        $links = $paginator->getLinkRange(3);
        $pages = (string)$paginator;
        $this->assertEquals(13, $paginator->getNumberOfPages());
    }

    public function testGetForm()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'page=2';
        $_GET = [
            'var' => 123
        ];
        $paginator = Paginator::createForm(127);
        $paginator->setInputSeparator(' o ');
        $links = $paginator->getFormString(13);
        $links = $paginator->getFormString(1);
        $links = $paginator->getFormString(2);
        $links = $paginator->getFormString(12);
        $pages = (string)$paginator;
        $this->assertEquals(13, $paginator->getNumberOfPages());
        $this->assertContains('<form', $pages);
    }

}
