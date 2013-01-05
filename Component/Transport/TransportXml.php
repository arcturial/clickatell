<?php
namespace Clickatell\Component\Transport;

use Clickatell\Component\Request as Request;
use Clickatell\Component\Transport as Transport;
use Clickatell\Exception\Diagnostic as Diagnostic;

/**
 * This is the XML interface to Clickatell. It wraps requests
 * into XML formatted blobs and handles the response from Clickatell
 * into a generic format.
 *
 * @package Clickatell\Component\Transport
 * @author Chris Brand
 */
class TransportXml extends Transport
{   
    /**
     * XML endpoint
     * @var string
     */
    const XML_ENDPOINT = "http://api.clickatell.com/xml/xml";

    /**
     * This builds the xml packet from the Request object
     * parameters.
     *
     * @param Clickatell\Component\Request $request
     * @return string
     */
    public function buildPost(Request $request)
    {
        $params = $request->getParams();
        $action = $params['action'];
        unset($params['action']);

        $xml = "<clickAPI>";
        $xml .= "<" . $action . ">";

        foreach ($params as $key => $val)
        {
            if ($val != "")
            {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }

        $xml .= "</" . $action . ">";
        $xml .= "</clickAPI>";
        
        return "data=" . urlencode($xml);
    }

    /**
     * The "sendMsg" XML call. Builds up the request and handles the response
     * from Clickatell.
     *
     * @param string $to
     * @param string $message
     * @param string $from
     * @param boolean $callback
     * @return array
     */
	public function sendMessage($to, $message, $from = "", $callback = true)
	{
        $this->request()->reset(); // clean request

        $this->request()->action = "sendMsg";

		$this->request()->to = $to;
		$this->request()->text = $message;
		$this->request()->from = $from;
		$this->request()->callback = $callback;

        $response = $this->transfer()->execute(self::XML_ENDPOINT, $this->buildPost($this->request()));

        #-> Convert response into xml object
        $xml = simplexml_load_string($response);
        $xml = $xml->sendMsgResp;

        #-> Check if it's a failure
        if (property_exists($xml, "fault"))
        {
            return $this->wrapResponse(Transport::RESULT_FAILURE, (string) $xml->fault);
        }
        else
        {
            $result = array();
            $result['apiMsgId'] = (string) $xml->apiMsgId;

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $result);
        }
	}

    /**
     * The "getBalance" XML call.
     *
     * @return array
     */
    public function getBalance()
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "getBalance";

        $response = $this->transfer()->execute(self::XML_ENDPOINT, $this->buildPost($this->request()));

        #-> Convert response into xml object
        $xml = simplexml_load_string($response);
        $xml = $xml->getBalanceResp;

        #-> Check if it's a failure
        if (property_exists($xml, "fault"))
        {
            return $this->wrapResponse(Transport::RESULT_FAILURE, (string) $xml->fault);
        }
        else
        {
            $result = array();
            $result['balance'] = (float) $xml->ok;

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $result);
        }
    }

    /**
     * The "queryMsg" XML call.
     *
     * @param string $apiMsgId
     * @return array
     */
    public function queryMessage($apiMsgId)
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "queryMsg";

        $this->request()->apiMsgId = $apiMsgId;

        $response = $this->transfer()->execute(self::XML_ENDPOINT, $this->buildPost($this->request()));

        #-> Convert response into xml object
        $xml = simplexml_load_string($response);
        $xml = $xml->queryMsgResp;

        #-> Check if it's a failure
        if (property_exists($xml, "fault"))
        {
            return $this->wrapResponse(Transport::RESULT_FAILURE, (string) $xml->fault);
        }
        else
        {
            $result = array();
            $result['apiMsgId'] = (string) $xml->apiMsgId;
            $result['status'] = trim((string) $xml->status);
            $result['description'] = Diagnostic::getError($result['status']);

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $result);
        }
    }

    /**
     * The "routeCoverage" XML call.
     *
     * @param int $msisdn
     * @return array
     */
    public function routeCoverage($msisdn)
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "routeCoverage";

        $this->request()->msisdn = $msisdn;

        $response = $this->transfer()->execute(self::XML_ENDPOINT, $this->buildPost($this->request()));

        #-> Convert response into xml object
        $xml = simplexml_load_string($response);
        $xml = $xml->routeCoverageResp;

        #-> Check if it's a failure
        if (property_exists($xml, "fault"))
        {
            return $this->wrapResponse(Transport::RESULT_FAILURE, (string) $xml->fault);
        }
        else
        {
            $result = array();
            $result['description'] = (string) $xml->ok;
            $result['charge'] = (float) $xml->charge;

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $result);
        }
    }

    /**
     * The "getMsgCharge" XML call.
     *
     * @param string $apiMsgId
     * @return array
     */
    public function getMessageCharge($apiMsgId)
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "getMsgCharge";

        $this->request()->apiMsgId = $apiMsgId;

        $response = $this->transfer()->execute(self::XML_ENDPOINT, $this->buildPost($this->request()));

        #-> Convert response into xml object
        $xml = simplexml_load_string($response);
        $xml = $xml->getMsgChargeResp;

        #-> Check if it's a failure
        if (property_exists($xml, "fault"))
        {
            return $this->wrapResponse(Transport::RESULT_FAILURE, (string) $xml->fault);
        }
        else
        {
            $result = array();
            $result['apiMsgId'] = (string) $xml->apiMsgId;
            $result['status'] = trim((string) $xml->status);
            $result['description'] = Diagnostic::getError($result['status']);
            $result['charge'] = (float) $xml->charge;

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $result);
        }
    }
}