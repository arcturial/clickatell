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
 * @package  Clickatell\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Transport;

use Clickatell\Component\Request as Request;
use Clickatell\Component\Transport as Transport;
use Clickatell\Exception\Diagnostic as Diagnostic;
use Clickatell\Component\Transport\TransportInterfaceExtract as TransportInterfaceExtract;

/**
 * This is the XML interface to Clickatell. It wraps requests
 * into XML formatted blobs and handles the response from Clickatell
 * into a generic format.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class TransportXml extends Transport implements TransportInterfaceExtract
{
    /**
     * XML endpoint
     * @var string
     */
    const XML_ENDPOINT = "http://api.clickatell.com/xml/xml";

    /**
     * Extracts the result from the framework into an associative
     * array.
     *
     * @param string $response Response string from API
     *
     * @return array
     */
    public function extract($response)
    {
        $iterator = new \SimpleXMLIterator($response);
        $iterator->rewind();
        
        $result = array();

        foreach ($iterator->getChildren() as $elementName => $node) {
            $result[$elementName] = (string) $node;
        }

        return $result;
    }

    /**
     * This builds the xml packet from the Request object
     * parameters.
     *
     * @param Clickatell\Component\Request $request Request object to process
     *
     * @return string
     */
    public function buildPost(Request $request)
    {
        $params = $request->getParams();
        $action = $params['action'];
        unset($params['action']);

        $xml = "<clickAPI>";
        $xml .= "<" . $action . ">";

        foreach ($params as $key => $val) {
            
            if ($val != "") {
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
     * @param string  $to       Recipient list
     * @param string  $message  Message
     * @param string  $from     From number (sender ID)
     * @param boolean $callback Should the callback be utilised
     * 
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

        $response = $this->transfer()->execute(
            self::XML_ENDPOINT, 
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->extract($response);

        // Check if it's a failure
        if (isset($result['fault'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['fault']
            );
            
        } else {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
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

        $response = $this->transfer()->execute(
            self::XML_ENDPOINT, 
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->extract($response);

        // Check if it's a failure
        if (isset($result['fault'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['fault']
            );

        } else {

            $packet = array();
            $packet['balance'] = (float) $result['ok'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "queryMsg" XML call.
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function queryMessage($apiMsgId)
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "queryMsg";

        $this->request()->apiMsgId = $apiMsgId;

        $response = $this->transfer()->execute(
            self::XML_ENDPOINT, 
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->extract($response);
        
        // Check if it's a failure
        if (isset($result['fault'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['fault']
            );

        } else {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];
            $packet['status'] = trim((string) $result['status']);
            $packet['description'] = Diagnostic::getError($result['status']);

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "routeCoverage" XML call.
     *
     * @param int $msisdn Number to check for coverage
     *
     * @return array
     */
    public function routeCoverage($msisdn)
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "routeCoverage";

        $this->request()->msisdn = $msisdn;

        $response = $this->transfer()->execute(
            self::XML_ENDPOINT, 
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->extract($response);

        // Check if it's a failure
        if (isset($result['fault'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['fault']
            );

        } else {

            $packet = array();
            $packet['description'] = (string) $result['ok'];
            $packet['charge'] = (float) $result['charge'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "getMsgCharge" XML call.
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function getMessageCharge($apiMsgId)
    {
        $this->request()->reset(); // clean request

        $this->request()->action = "getMsgCharge";

        $this->request()->apiMsgId = $apiMsgId;

        $response = $this->transfer()->execute(
            self::XML_ENDPOINT, 
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->extract($response);

        // Check if it's a failure
        if (isset($result['fault'])) {
            
            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['fault']
            );

        } else {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];
            $packet['status'] = trim((string) $result['status']);
            $packet['description'] = Diagnostic::getError($result['status']);
            $packet['charge'] = (float) $result['charge'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }
}