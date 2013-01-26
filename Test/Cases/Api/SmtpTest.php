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
 * @package  Clickatell\Test\Cases\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Api;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
use Clickatell\Exception\Diagnostic as Diagnostic;

/**
 * Test Suite for testing the API calls on the
 * SMTP API interface.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class SmtpTest extends PHPUnit_Framework_TestCase
{
    /**
     * The transport object used, Instance of SMTP API
     * @var Clickatell\Api\Soap
     */
    private $_transport = null;

    /**
     * Setup some default behaviour. We want
     * to mock the curl dependency
     *
     * @return boolean
     */
    public function setUp()
    {
        // Mock the translate interface
        $translate = $this->getMock(
            "Clickatell\Component\Translate\TranslateInterface"
        );

        // Mock only the curl call
        $this->_transport = $this->getMock(
            'Clickatell\Api\Smtp',
            array('callApi', 'extract'),
            array($translate)
        );
    }

    /**
     * Ensures that "sendMsg" SMTP call is working as 
     * expected and returns the correctly wrapped array 
     * for a successful call.
     *
     * @return boolean.
     */
    public function testSendMessage()
    {
        $to = array(12345);
        $message = "My Message";
        $apiMsgId = '-1';

        $this->_transport->expects($this->once())
            ->method('callApi')
            ->will($this->returnValue(true));

        $this->_transport->expects($this->once())
            ->method('extract');

        $result = $this->_transport->sendMessage($to, $message);
        
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['result']['response']['apiMsgId']));
        $this->assertSame($apiMsgId, $result['result']['response']['apiMsgId']);
    }
}