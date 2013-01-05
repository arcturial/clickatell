<?php
namespace Clickatell\Test\Cases\Component;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use Clickatell\Component\Action as Action;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the Action handler and ensures
 * that it correctly wraps the Transport and Translate interfaces.
 *
 * @package Clickatell\Test\Cases\Component
 * @author Chris Brand
 */
class ActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that when an Action is called, it routes through the Transport
     * interface and then routes the result through the Translate interface.
     *
     * @return boolean
     */
    public function testActionCallable()
    {
        $result = array("method callable");

        $transport = $this->getMock("Clickatell\Component\Transport\TransportInterface");
        $transport->expects($this->any())
                  ->method('sendMessage')
                  ->will($this->returnValue($result));

        $translate = $this->getMock("Clickatell\Component\Translate\TranslateInterface");
        $translate->expects($this->any())
                  ->method('translate')
                  ->with($this->equalTo($result))
                  ->will($this->returnValue($result));

        $action = new Action($transport, $translate);

        $this->assertSame($result, $action->sendMessage(12345, "My Message"));
    }
}