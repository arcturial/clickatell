<?php
namespace Clickatell\Api;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

class ClickatellHttpTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $uri = "http/sendmsg";
        $args = array(
            'user'      => 'username',
            'password'  => 'password',
            'api_id'    => '123456'
        );

        $clickatell = $this->getMockBuilder('Clickatell\Api\ClickatellHttp')
            ->setMethods(array('curl'))
            ->setConstructorArgs(array('username', 'password', '123456'))
            ->getMock();

        $clickatell->expects($this->once())
            ->method('curl')
            ->with($uri, $args);

        $class = new ReflectionClass($clickatell);
        $method = $class->getMethod('get');
        $method->setAccessible(true);
        $method->invokeArgs($clickatell, array($uri, array()));
    }

    public function testSendMessage()
    {
        $default = array(
            'to'        => "12345,123456",
            'text'      => 'message',
            'mo'        => false,
            'callback'  => true
        );

        $clickatell = $this->getMockBuilder('Clickatell\Api\ClickatellHttp')
            ->setMethods(array('unwrapLegacy', 'get'))
            ->disableOriginalConstructor()
            ->getMock();

        $response = array(
            'body' => 'response body'
        );

        $clickatell->expects($this->once())
            ->method('get')
            ->with('http/sendmsg', $default)
            ->will($this->returnValue($response));

        $responseEntry = array(
            array(
                'ID'    => '123456789',
                'To'    => '12345',
                'code'  => '103',
                'error' => 'Error message'
            )
        );

        $clickatell->expects($this->once())
            ->method('unwrapLegacy')
            ->with($response['body'], true)
            ->will($this->returnValue($responseEntry));

        $entries = $clickatell->sendMessage(array(12345, 123456), "message", array('mo' => false));

        $this->assertSame($responseEntry[0]['ID'], $entries[0]->id);
        $this->assertSame($responseEntry[0]['To'], $entries[0]->to);
        $this->assertSame($responseEntry[0]['code'], $entries[0]->errorCode);
        $this->assertSame($responseEntry[0]['error'], $entries[0]->error);
    }
}