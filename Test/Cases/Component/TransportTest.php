<?php
namespace Clickatell\Test\Cases\Component;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use Clickatell\Component\Action as Action;
use Clickatell\Component\Transport as Transport;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the transport abstract. 
 *
 * @package Clickatell\Test\Cases\Component
 * @author Chris Brand
 */
class TransportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the transport wrapper wraps the response as
     * expected.
     *
     * @return boolean
     */
    public function testWrapResponse()
    {
        $status = Transport::RESULT_SUCCESS;
        $response = "My Response String";

        $expectedResult = array("result" => array("status" => $status, "response" => $response));

        $transport = $this->getMockBuilder('Clickatell\Component\Transport')
                        ->disableOriginalConstructor()
                        ->getMockForAbstractClass();

        $transport->expects($this->any())
                  ->method('wrapResponse')
                  ->will($this->returnValue($expectedResult));

        $this->assertSame($expectedResult, $transport->wrapResponse($status, $response));
    }
}