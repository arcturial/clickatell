<?php
/**
 * The Clickatell SMS Library provides a standardised way of talking to and
 * receiving replies from the Clickatell API's. It makes it
 * easier to write your applications and grants the ability to
 * quickly switch the type of API you want to use HTTP/XML without
 * changing any code.
 *
 * PHP Version 5.3
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Component;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use Clickatell\Component\Action as Action;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the Action handler and ensures
 * that it correctly wraps the Transport and Translate interfaces.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
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

        $transport = $this->getMock(
            "Clickatell\Component\Transport\TransportInterface"
        );

        $transport->expects($this->any())
            ->method('sendMessage')
            ->will($this->returnValue($result));

        $translate = $this->getMock(
            "Clickatell\Component\Translate\TranslateInterface"
        );
        
        $translate->expects($this->any())
            ->method('translate')
            ->with($this->equalTo($result))
            ->will($this->returnValue($result));

        $action = new Action($transport, $translate);

        $this->assertSame($result, $action->sendMessage(12345, "My Message"));
    }
}