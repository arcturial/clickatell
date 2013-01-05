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
     * Test that the messaging object was initiated with the HTTP
     * transport protocol as default.
     *
     * @return boolean
     */
    public function testRequestCreated()
    {
        $clickatell = new Clickatell("username", "password", 12345);

        $this->assertInstanceOf(
            "Clickatell\Component\Request", 
            $clickatell->request()
        );
    }

    /**
     * Make sure you can change the transport protocol after the messaging
     * object has been created. It also checks to ensure that you can
     * only specify the transports that are supported.
     *
     * @return boolean
     */
    public function testInitialTransport()
    {
        $clickatell = new Clickatell("username", "password", 12345);

        // Set the transport and ensure we get a chained object back.
        $this->assertInstanceOf(
            "Clickatell\Component\Transport\TransportHttp", 
            $clickatell->getTransport()
        );
    }

    /**
     * Ensure that a call to the messenger follows the correct path through
     * the Transport and Transfer interfaces and returns the expected
     * result.
     *
     * @return boolean
     */
    public function testTransportDispatch()
    {
        $result = array("dispatch done");

        $clickatell = new Clickatell("username", "password", 12345);

        $transport = $this->getMock(
            "Clickatell\Component\Transport\TransportInterface"
        );

        $transport->expects($this->any())
            ->method('sendMessage')
            ->will($this->returnValue($result));

        $clickatell->setTransport($transport);

        $translate = $this->getMock(
            "Clickatell\Component\Translate\TranslateInterface"
        );

        $translate->expects($this->any())
            ->method('translate')
            ->with($this->equalTo($result))
            ->will($this->returnValue($result));

        $clickatell->setTranslate($translate);

        $this->assertSame($result, $clickatell->sendMessage(12345, "Test Message"));
    }
}