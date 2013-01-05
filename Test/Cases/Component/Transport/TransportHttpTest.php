<?php
namespace Clickatell\Test\Cases\Component\Transport;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../autoload.php";

use Clickatell\Component\Transport\TransportHttp as TransportHttp;
use Clickatell\Component\Transport as Transport;
use Clickatell\Exception\Diagnostic as Diagnostic;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TransportHttp class. This
 * ensures that all the HTTP API requests response as expected
 * and that the utility functions handle it's input/output correctly.
 *
 * @package Clickatell\Test\Cases\Component\Transport
 * @author Chris Brand
 */
class TransportHttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the buildPost function for the HTTP class
     * translates the Request parameter array into a query string.
     *
     * @return boolean
     */
    public function testBuildPost()
    {
    	$transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
        		->method('getParams')
                ->will($this->returnValue(array("to" => 12345)));

        $transport = new TransportHttp($transfer, $request);
        $result = $transport->buildPost($request);

        $this->assertSame("to=12345", $result);
    }

    /**
     * HTTP responses are a bit sporadic. This ensures that the function responsible
     * for taking the string and parsing it into an array is still working as expected.
     *
     * @return boolean
     */
    public function testExtract()
    {
      	$transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $transport = new TransportHttp($transfer, $request);
        $result = $transport->extract("OK: Ok Message Credit: 5");

        $this->assertSame(array("OK" => "Ok Message", "Credit" => "5"), $result);  
    }

    /**
     * Ensures that "sendMsg" HTTP call is working as expected and returns the correctly
     * wrapped array for a successful call.
     *
     * @return boolean.
     */
    public function testSendMessage()
    {
        $to = 12345;
        $message = "My Message";
        $apiMsgId = "1234567890";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("to" => $to, "text" => $message)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportHttp::ENDPOINT_SEND_MSG), "to=" . $to . "&text=" . $message)
                 ->will($this->returnValue("ID: " . $apiMsgId));


        $transport = new TransportHttp($transfer, $request);
        $result = $transport->sendMessage($to, $message);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['apiMsgId'], $apiMsgId);    
    }

    /**
     * Ensures that "getBalance" HTTP call is still working the way it should.
     *
     * @return boolean
     */
    public function testGetBalance()
    {
        $balance = "5";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array()));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportHttp::ENDPOINT_GET_BALANCE), "")
                 ->will($this->returnValue("Credit: " . $balance));


        $transport = new TransportHttp($transfer, $request);
        $result = $transport->getBalance();

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['balance'], (float) $balance);    
    }

    /**
     * Ensures that "queryMsg" HTTP call is still working as expected and returns the
     * results we want.
     *
     * @return boolean
     */
    public function testQueryMessage()
    {
        $status = "001";
        $status_msg = Diagnostic::getError($status);
        $apiMsgId = "1234567890";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("apiMsgId" => $apiMsgId)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportHttp::ENDPOINT_QUERY_MESSAGE), "apiMsgId=" . $apiMsgId)
                 ->will($this->returnValue("ID: " . $apiMsgId . " Status: 001"));


        $transport = new TransportHttp($transfer, $request);
        $result = $transport->queryMessage($apiMsgId);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['apiMsgId'], $apiMsgId);
        $this->assertSame($result['result']['response']['status'], $status);  
        $this->assertSame($result['result']['response']['description'], $status_msg);  
    }

    /**
     * Tests the "routeCoverage" HTTP call and ensures the response is wrapped correctly.
     *
     * @return boolean
     */
    public function testRouteCoverage()
    {
        $message = "My Message";
        $msisdn = "27721234567";
        $charge = 1;

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("msisdn" => $msisdn)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportHttp::ENDPOINT_ROUTE_COVERAGE), "msisdn=" . $msisdn)
                 ->will($this->returnValue("OK: " . $message. " Charge: " . $charge));


        $transport = new TransportHttp($transfer, $request);
        $result = $transport->routeCoverage($msisdn);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['description'], $message); 
        $this->assertSame($result['result']['response']['charge'], (float) $charge);   
    }

    /**
     * Tests the "getMsgCharge" HTTP call.
     *
     * @return boolean
     */
    public function testMessageCharge()
    {
        $status = "001";
        $status_msg = Diagnostic::getError($status);
        $charge = 1;
        $apiMsgId = "1234567890";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("apiMsgId" => $apiMsgId)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportHttp::ENDPOINT_MESSAGE_CHARGE), "apiMsgId=" . $apiMsgId)
                 ->will($this->returnValue("apiMsgId: " . $apiMsgId . " status: " . $status . " charge: " . $charge));


        $transport = new TransportHttp($transfer, $request);
        $result = $transport->getMessageCharge($apiMsgId);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['apiMsgId'], $apiMsgId);   
        $this->assertSame($result['result']['response']['status'], $status);  
        $this->assertSame($result['result']['response']['description'], $status_msg);   
        $this->assertSame($result['result']['response']['charge'], (float) $charge);   
    }

    /**
     * Tests and failed response and ensures it wraps the error message correctly.
     *
     * @return boolean
     */
    public function testResultError()
    {
        $error = "Error Message";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array()));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportHttp::ENDPOINT_SEND_MSG), "")
                 ->will($this->returnValue("ERR: " . $error));


        $transport = new TransportHttp($transfer, $request);
        $result = $transport->sendMessage(12345, "");

        $this->assertSame($result['result']['status'], Transport::RESULT_FAILURE);  
        $this->assertSame($result['result']['response'], $error);    
    }
}