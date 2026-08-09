<?php

namespace Pop\Paginator\Test;

use Pop\Paginator\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
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

    public function testWrapLinks()
    {
        $paginator = Paginator::createRange(20);
        $links     = $paginator->wrapLinks('div', 'link-on', 'link-off');
        $this->assertEquals('<div class="link-off"><span>1</span></div>', $links[0]);
        $this->assertEquals('<div class="link-on"><a href="?page=2">2</a></div>', $links[1]);
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
        $this->assertEquals(' o ', $paginator->getInputSeparator());
        $this->assertEquals(13, $paginator->getNumberOfPages());
        $this->assertStringContainsString('<form', $pages);
    }

    public function testFormEscapesHiddenInputValues()
    {
        $_SERVER['REQUEST_URI']  = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'q=malicious';
        $_GET = [
            'q' => '"><script>alert(1)</script>'
        ];
        $paginator = Paginator::createForm(100);
        $form      = $paginator->getFormString(1);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $form);
        $this->assertStringContainsString('&lt;script&gt;', $form);
    }

    public function testFormEscapesRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php"><script>alert(1)</script>';
        unset($_SERVER['QUERY_STRING']);
        $_GET = [];
        $paginator = Paginator::createForm(100);
        $form      = $paginator->getFormString(1);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $form);
    }

    public function testRangeEscapesRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php"><script>alert(1)</script>';
        unset($_SERVER['QUERY_STRING']);
        $_GET = [];
        $paginator = Paginator::createRange(30);
        $links     = $paginator->getLinkRange(1);
        $html      = implode('', $links);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function testGetNumberOfPagesBeforeRendering()
    {
        $paginator = Paginator::createRange(95, 10);
        $this->assertEquals(10, $paginator->getNumberOfPages());
    }

    public function testGetSeparatorDefault()
    {
        $paginator = Paginator::createRange(100);
        $this->assertEquals('', $paginator->getSeparator());
    }

    public function testGetClassOnClassOffDefault()
    {
        $paginator = Paginator::createRange(100);
        $this->assertEquals('', $paginator->getClassOn());
        $this->assertEquals('', $paginator->getClassOff());
    }

    public function testConstructorThrowsExceptionForZeroPerPage()
    {
        $this->expectException(\Pop\Paginator\Exception::class);
        Paginator::createRange(100, 0);
    }

    public function testConstructorThrowsExceptionForNegativeRange()
    {
        $this->expectException(\Pop\Paginator\Exception::class);
        Paginator::createRange(100, 10, -1);
    }

    public function testConstructorThrowsExceptionForNegativeTotal()
    {
        $this->expectException(\Pop\Paginator\Exception::class);
        Paginator::createRange(-1);
    }

    public function testCalculateRangeOnLastPageWithNoRemainder()
    {
        $paginator = Paginator::createRange(200, 10, 10);
        $links     = $paginator->getLinkRange(20);

        $this->assertEquals(20, $paginator->getNumberOfPages());
        $this->assertStringContainsString('<span>20</span>', implode('', $links));
    }

    public function testCalculateRangeResetsAndBuildsPrevBookendsForOutOfBoundsPage()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        unset($_SERVER['QUERY_STRING']);
        $_GET = [];
        $paginator = Paginator::createRange(50, 10, 10);
        $links     = $paginator->getLinkRange(999);
        $html      = implode('', $links);

        $this->assertEquals(5, $paginator->getNumberOfPages());
        $this->assertEquals(999, $paginator->getCurrentPage());
        $this->assertStringContainsString($paginator->getBookend('start'), $html);
        $this->assertStringContainsString($paginator->getBookend('previous'), $html);
    }

    public function testRangeUnsetsQueryKeyFromPreservedGetParams()
    {
        $_SERVER['REQUEST_URI']  = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'page=2&foo=bar';
        $_GET = [
            'page' => 2,
            'foo'  => 'bar'
        ];
        $paginator = Paginator::createRange(50);
        $links     = $paginator->getLinkRange(2);
        $html      = implode('', $links);

        $this->assertStringContainsString('foo=bar', $html);
        $this->assertSame(0, substr_count($html, 'page=2'));
    }

    public function testRangeToStringGeneratesLinksWhenNotYetGenerated()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        unset($_SERVER['QUERY_STRING']);
        $_GET = [];
        $paginator = Paginator::createRange(30);

        $this->assertStringContainsString('<span>1</span>', (string)$paginator);
    }

    public function testFormGetFormStringUsesGetParamWhenPageOmitted()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        unset($_SERVER['QUERY_STRING']);
        $_GET = [
            'page' => 3
        ];
        $paginator = Paginator::createForm(100);
        $form      = $paginator->getFormString();

        $this->assertEquals(3, $paginator->getCurrentPage());
        $this->assertStringContainsString('value="3"', $form);
    }

    public function testFormUnsetsQueryKeyFromPreservedGetParams()
    {
        $_SERVER['REQUEST_URI']  = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'page=2&foo=bar';
        $_GET = [
            'page' => 2,
            'foo'  => 'bar'
        ];
        $paginator = Paginator::createForm(50);
        $form      = $paginator->getFormString(2);

        $this->assertStringContainsString('name="foo" value="bar"', $form);
        $this->assertStringNotContainsString('type="hidden" name="page"', $form);
    }

    public function testFormHandlesArrayValuedGetParams()
    {
        $_SERVER['REQUEST_URI']  = '/pages.php';
        $_SERVER['QUERY_STRING'] = 'filter[status]=active';
        $_GET = [
            'filter' => ['status' => 'active']
        ];
        $paginator = Paginator::createForm(50);
        $form      = $paginator->getFormString(1);

        $this->assertStringContainsString('name="filter[status]" value="active"', $form);
    }

    public function testFormToStringGeneratesFormWhenNotYetGenerated()
    {
        $_SERVER['REQUEST_URI'] = '/pages.php';
        unset($_SERVER['QUERY_STRING']);
        $_GET = [];
        $paginator = Paginator::createForm(30);

        $this->assertStringContainsString('<form', (string)$paginator);
    }

}
