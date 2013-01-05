<?php
namespace Clickatell\Test\Cases\Component;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use Clickatell\Component\Request as Request;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the Request object to ensure
 * it can store parameters as expected.
 *
 * @package Clickatell\Test\Cases\Component
 * @author Chris Brand
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