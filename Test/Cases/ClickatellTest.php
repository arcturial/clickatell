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
 * @package  Clickatell\Test\Cases
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../autoload.php";

use Clickatell\Clickatell as Clickatell;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
use \ReflectionClass as ReflectionClass;

/**
 * Test Suite for testing the main messenger functionality. The messenger
 * serves as a container to bring the different objects used together and
 * executing them as needed.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ClickatellTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensure the autoloading functionality is working.
     *
     * @return boolean
     */
    public function testAutoloader()
    {
        $clickatell = new Clickatell(
            "username",
            "password",
            12345,
            Clickatell::HTTP_API
        );

        // Make the private method accesible
        $reflection = new ReflectionClass($clickatell);
        $method = $reflection->getMethod('_autoLoad');
        $method->setAccessible(true);

        // Load a random clickatell library component
        $this->assertTrue(
            $method->invokeArgs(
                $clickatell,
                array('Clickatell\Exception\Diagnostic')
            )
        );

        $this->assertFalse(
            $method->invokeArgs(
                $clickatell,
                array('Clickatell\Exception\Unknown')
            )
        );
    }

    /**
     * Make sure you can change the transport protocol after the messaging
     * object has been created. It also checks to ensure that you can
     * only specify the transports that are supported.
     *
     * @return boolean
     */
    public function testTransportCreation()
    {
        $clickatell = new Clickatell(
            "username",
            "password",
            12345,
            Clickatell::HTTP_API
        );

        // Set the transport and ensure we get a chained object back.
        $this->assertInstanceOf(
            "Clickatell\Api\Http",
            $clickatell->getTransport()
        );
    }

    /**
     * Ensure that a call to the messenger follows the correct path through
     * the Transport and Translate interfaces and returns the expected
     * result.
     *
     * @return boolean
     */
    public function testTransportDispatch()
    {
        // Set expected result
        $result = array("result" => "call done");

        $clickatell = new Clickatell(
            "username",
            "password",
            12345,
            Clickatell::HTTP_API
        );

        // Mock the API transport with an interface
        $transport = $this->getMockBuilder("Clickatell\Api\Http")
            ->disableOriginalConstructor()
            ->setMethods(array("call"))
            ->getMock();

        $transport->expects($this->any())
            ->method('call')
            ->will($this->returnValue($result));

        // Set the mocked transport
        $clickatell->setTransport($transport);


        $this->assertSame(
            $result,
            $clickatell->sendMessage(array(12345), "Test Message")
        );
    }


    /**
     * Ensure that the _methodExists function correctly scans an
     * interface for the applicable method.
     *
     * @return boolean
     */
    public function testMethodExists()
    {
        $clickatell = new Clickatell(
            "username",
            "password",
            12345,
            Clickatell::HTTP_API
        );

        // Make the private method accesible
        $reflection = new ReflectionClass($clickatell);
        $method = $reflection->getMethod('_methodExists');
        $method->setAccessible(true);

        // Test existing method
        $this->assertTrue(
            $method->invokeArgs(
                $clickatell,
                array(array('Clickatell\Api\Definition\ApiInterface'), 'sendMessage')
            )
        );

        // Test unknown method
        $this->assertFalse(
            $method->invokeArgs(
                $clickatell,
                array(array('Clickatell\Api\Definition\ApiInterface'), 'sendUnknown')
            )
        );
    }

    /**
     * Test the parsing of callback information.
     *
     * @return boolean
     */
    public function testParseCallback()
    {
        $_GET = array(
            'apiMsgId' => 1234,
            'cliMsgId' => 12345,
            'to' => 12345678,
            'timestamp' => 0987654321,
            'from' => 651234515,
            'status' => '003',
            'charge' => 0
        );


        $mock = $this->getMock('stdClass', array('callback'));
        $mock->expects($this->once())
            ->method('callback')
            ->with($this->equalTo($_GET));

        $func = function ($values) use ($mock) {
            $mock->callback($values);
        };


        // Test
        Clickatell::parseCallback($func);
    }
}