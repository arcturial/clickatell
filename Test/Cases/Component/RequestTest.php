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

use Clickatell\Component\Request as Request;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the Request object to ensure
 * it can store parameters as expected.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the magic functionality of setting parameters
     *
     * @return boolean
     */
    public function testSetParams()
    {
        $request = new Request("username", "password", 12345);

        $to = 123456789;
        $message = "test message";

        $request->to = $to;
        $request->message = $message;

        $params = $request->getParams();

        $this->assertTrue(is_array($params));

        $this->assertSame($to, $params['to']);
        $this->assertSame($message, $params['message']);
    }

    /**
     * Ensures that when resetting the request object, the
     * username/password/apiID is persistent.
     *
     * @return boolean
     */
    public function testReset()
    {
        $request = new Request("username", "password", 12345);
        
        $to = 123456789;

        $request->to = $to;

        $this->assertInstanceOf("Clickatell\Component\Request", $request->reset());

        $params = $request->getParams();

        $this->assertFalse(isset($params['to']));

        $this->assertTrue(isset($params['user']) && !empty($params['user']));
        $this->assertTrue(isset($params['password']) && !empty($params['password']));
        $this->assertTrue(isset($params['api_id']) && !empty($params['api_id']));
    }
}