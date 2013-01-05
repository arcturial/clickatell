<?php
namespace Clickatell\Test\Cases\Component\Transport;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../autoload.php";

use Clickatell\Component\Transport\TransportXml as TransportXml;
use Clickatell\Exception\Diagnostic as Diagnostic;
use Clickatell\Component\Transport as Transport;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TransportXml class. Ensures
 * that it wraps the XML packets the way we expect.
 *
 * @package Clickatell\Test\Cases\Component\Transport
 * @author Chris Brand
 */
class TransportXmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Utility function for wrapping XML packets for testing.
     * They are wrapped the way Clickatell expects them.
     *
     * @param string $action
     * @param array $param
     * @return string
     */
    private function _buildXmlPacket($action, array $param)
    {
        $result = "<clickAPI>";
        $result .= "<".$action.">";

        foreach ($param as $key => $val)
        {
            $result .= "<".$key.">".$val."</".$key.">";
        }

        $result .= "</".$action.">";
        $result .= "</clickAPI>";

        return "data=".urlencode($result);
    }

    /**
     * Utility function for wrapping XML response packets for testing.
     * They are wrapped the way Clickatell responds from their API.
     *
     * @param string $action
     * @param array $param
     * @return string
     */
    private function _buildXmlReturnPacket($action, array $param)
    {
        $result = "<clickAPI>";
        $result .= "<".$action."Resp>";

        foreach ($param as $key => $val)
        {
            $result .= "<".$key.">".$val."</".$key.">";
        }

        $result .= "</".$action."Resp>";
        $result .= "</clickAPI>";   

        return $result;
    }

    /**
     * Test the functionality to convert the xml response to an
     * associative array.
     *
     * @return boolean
     */
    public function testExtract()
    {
        $apiMsgId = "1234567890";

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $transport = new TransportXml($transfer, $request);
        $result = $transport->extract("<clickAPI><sendMsgResp><apiMsgId>" . $apiMsgId . "</apiMsgId></sendMsgResp></clickAPI>");

        $this->assertSame(array("apiMsgId" => $apiMsgId), $result);  
    }

    /**
     * Ensures that the buildPost() method for the XML transport builds the
     * packet the way Clickatell expects it.
     *
     * @return boolean.
     */
    public function testBuildPost()
    {
    	$transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $packet = array(
            "action" => "actionname",
            "to" => 12345
        );

        $request->expects($this->any())
        		->method('getParams')
                ->will($this->returnValue($packet));

        $transport = new TransportXml($transfer, $request);
        $result = $transport->buildPost($request);

        #-> Get xml
        preg_match("/data=(.*)/", $result, $match);

        $xml = simplexml_load_string(urldecode($match[1]));

        $this->assertTrue(property_exists($xml, 'actionname'));
        $this->assertTrue(property_exists($xml->actionname, 'to'));
        $this->assertSame(12345, (int) $xml->actionname->to);
    }

    /**
     * Test the "sendMsg" XML call.
     *
     * @return boolean
     */
    public function testSendMessage()
    {
        $action = "sendMsg";
        $to = 12345;
        $message = "My Message";
        $apiMsgId = "1234567890";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("action" => $action, "to" => $to, "text" => $message)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportXml::XML_ENDPOINT), $this->_buildXmlPacket($action, array("to" => $to, "text" => $message)))
                 ->will($this->returnValue($this->_buildXmlReturnPacket($action, array("apiMsgId" => $apiMsgId))));

        $transport = new TransportXml($transfer, $request);
        $result = $transport->sendMessage($to, $message);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['apiMsgId'], $apiMsgId);    
    }

    /**
     * Test the "getBalance" XML call.
     *
     * @return boolean
     */
    public function testGetBalance()
    {
        $action = "getBalance";
        $balance = "5";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("action" => $action)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportXml::XML_ENDPOINT), $this->_buildXmlPacket($action, array()))
                 ->will($this->returnValue($this->_buildXmlReturnPacket($action, array("ok" => $balance))));


        $transport = new TransportXml($transfer, $request);
        $result = $transport->getBalance();

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['balance'], (float) $balance);    
    }

    /**
     * Test the "queryMsg" XML call.
     * 
     * @return boolean
     */
    public function testQueryMessage()
    {
        $action = "queryMsg";
        $status = "001";
        $status_msg = Diagnostic::getError($status);
        $apiMsgId = "1234567890";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("action" => $action, "apiMsgId" => $apiMsgId)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportXml::XML_ENDPOINT), $this->_buildXmlPacket($action, array("apiMsgId" => $apiMsgId)))
                 ->will($this->returnValue($this->_buildXmlReturnPacket($action, array("apiMsgId" => $apiMsgId, "status" => $status))));


        $transport = new TransportXml($transfer, $request);
        $result = $transport->queryMessage($apiMsgId);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['apiMsgId'], $apiMsgId);
        $this->assertSame($result['result']['response']['status'], $status);  
        $this->assertSame($result['result']['response']['description'], $status_msg);   
    }

    /**
     * Test the "routeCoverage" XML call.
     *
     * @return boolean
     */
    public function testRouteCoverage()
    {
        $action = "routeCoverage";
        $message = "My Message";
        $msisdn = "27721234567";
        $charge = 1;

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("action" => $action, "msisdn" => $msisdn)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportXml::XML_ENDPOINT), $this->_buildXmlPacket($action, array("msisdn" => $msisdn)))
                 ->will($this->returnValue($this->_buildXmlReturnPacket($action, array("ok" => $message, "charge" => $charge))));


        $transport = new TransportXml($transfer, $request);
        $result = $transport->routeCoverage($msisdn);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['description'], $message); 
        $this->assertSame($result['result']['response']['charge'], (float) $charge);   
    }

    /**
     * Test the "getMsgCharge" XML call.
     *
     * @return boolean
     */
    public function testMessageCharge()
    {
        $action = "getMsgCharge";
        $status = "001";
        $status_msg = Diagnostic::getError($status);
        $charge = 1;
        $apiMsgId = "1234567890";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("action" => $action, "apiMsgId" => $apiMsgId)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportXml::XML_ENDPOINT), $this->_buildXmlPacket($action, array("apiMsgId" => $apiMsgId)))
                 ->will($this->returnValue($this->_buildXmlReturnPacket($action, array("apiMsgId" => $apiMsgId, "status" => $status, "charge" => $charge))));


        $transport = new TransportXml($transfer, $request);
        $result = $transport->getMessageCharge($apiMsgId);

        $this->assertSame($result['result']['status'], Transport::RESULT_SUCCESS);  
        $this->assertSame($result['result']['response']['apiMsgId'], $apiMsgId);   
        $this->assertSame($result['result']['response']['status'], $status);   
        $this->assertSame($result['result']['response']['description'], $status_msg);  
        $this->assertSame($result['result']['response']['charge'], (float) $charge);   
    }

    /**
     * Test an failed response for a XML call and ensures it's warpped correctly.
     *
     * @return boolean
     */
    public function testResultError()
    {
        $action = "sendMsg";
        $error = "Error Message";

        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('getParams')
                ->will($this->returnValue(array("action" => $action)));

        $transfer = $this->getMock("Clickatell\Component\Transfer\TransferInterface");
        $transfer->expects($this->any())
                 ->method("execute")
                 ->with($this->equalTo(TransportXml::XML_ENDPOINT), $this->_buildXmlPacket($action, array()))
                 ->will($this->returnValue($this->_buildXmlReturnPacket($action, array("fault" => $error))));


        $transport = new TransportXml($transfer, $request);
        $result = $transport->sendMessage(12345, "");

        $this->assertSame($result['result']['status'], Transport::RESULT_FAILURE);  
        $this->assertSame($result['result']['response'], $error);    
    }
}