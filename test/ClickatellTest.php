<?php
namespace Clickatell;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

class ClickatellTest extends PHPUnit_Framework_TestCase
{
    public function testCurl()
    {
        $uri = "http/sendmsg";
        $args = array();

        $clickatell = $this->getMockForAbstractClass('Clickatell\Clickatell');

        $class = new ReflectionClass($clickatell);
        $method = $class->getMethod('curl');
        $method->setAccessible(true);
        $return = $method->invokeArgs($clickatell, array($uri, $args));

        $this->assertSame(200, $return['code']);
        $this->assertTrue(strlen($return['body']) > 0);
    }

    public function testUnwrapLegacy()
    {
        $body = "ERR: 301, Some Error";

        $clickatell = $this->getMockForAbstractClass('Clickatell\Clickatell');

        $class = new ReflectionClass($clickatell);
        $method = $class->getMethod('unwrapLegacy');
        $method->setAccessible(true);
        $return = $method->invokeArgs($clickatell, array($body, false, false));

        $this->assertSame('Some Error', $return['error']);
        $this->assertEquals(301, $return['code']);

        $this->setExpectedException('Exception');
        $method->invokeArgs($clickatell, array($body, false, true));
    }
}