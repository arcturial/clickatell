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

use Clickatell\Component\Transport as Transport;
use Clickatell\Component\Request as Request;
use Clickatell\Exception\Diagnostic as Diagnostic;

/**
 * This is the HTTP interface to Clickatell. It transforms
 * the Request object into a suitable query string and
 * handles the response from Clickatell.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class TransportHttp extends Transport implements TransportInterfaceExtract
{
    /**
     * "sendMsg" endpoint
     * @var string
     */
    const ENDPOINT_SEND_MSG = "http://api.clickatell.com/http/sendmsg";

    /**
     * "getbalance" endpoint
     * @var string
     */
    const ENDPOINT_GET_BALANCE = "http://api.clickatell.com/http/getbalance";

    /**
     * "querymsg" endpoint
     * @var string
     */
    const ENDPOINT_QUERY_MESSAGE = "http://api.clickatell.com/http/querymsg";

    /**
     * "routeCoverage" endpoint
     * @var string
     */
    const ENDPOINT_ROUTE_COVERAGE = "http://api.clickatell.com/utils/routeCoverage";

    /**
     * "getmsgcharge" endpoint
     * @var string
     */
    const ENDPOINT_MESSAGE_CHARGE = "http://api.clickatell.com/http/getmsgcharge";

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
        preg_match_all("/([A-Za-z]+):((.(?![A-Za-z]+:))*)/", $response, $matches);

        $result = array();

        foreach ($matches[1] as $index => $status) {
            $result[$status] = trim($matches[2][$index]);
        }

        return $result;
    }
    
    /**
     * Morphs the Request object parameters into
     * a suitable query string.
     * 
     * @param Clickatell\Component\Request $request Request object to process
     *
     * @return string
     */
    public function buildPost(Request $request)
    {
        $params = $request->getParams();

        $data = "";

        foreach ($params as $key => $val) {

            if ($val != "") {
                $data .= $key . "=" . $val . "&";
            }
        }

        $data = trim($data, "&");

        return $data;
    }

    /**
     * The "sendMsg" HTTP call. Builds up the request and handles the response
     * from Clickatell.
     *
     * @param string  $to       Recipient list
     * @param string  $message  Message
     * @param string  $from     From address (sender ID)
     * @param boolean $callback Use callback or not
     *
     * @return array
     */
    public function sendMessage($to, $message, $from = "", $callback = true)
    {
        $this->request()->to = $to;
        $this->request()->text = $message;
        $this->request()->from = $from;
        $this->request()->callback = $callback;

        $response = $this->transfer()->execute(
            self::ENDPOINT_SEND_MSG, 
            $this->buildPost($this->request())
        );

        $result = $this->extract($response);

        if (isset($result['ERR'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['ERR']
            );

        } else {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['ID'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "getBalance" HTTP call.
     *
     * @return array
     */
    public function getBalance()
    {
        $response = $this->transfer()->execute(
            self::ENDPOINT_GET_BALANCE, 
            $this->buildPost($this->request())
        );

        $result = explode(":", $response);

        $result = $this->extract($response);

        if (isset($result['ERR'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['ERR']
            );

        } else {

            $packet = array();
            $packet['balance'] = (float) $result['Credit'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "queryMsg" HTTP call.
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function queryMessage($apiMsgId)
    {
        $this->request()->apiMsgId = $apiMsgId;

        $response = $this->transfer()->execute(
            self::ENDPOINT_QUERY_MESSAGE, 
            $this->buildPost($this->request())
        );

        $result = $this->extract($response);

        if (isset($result['ERR'])) {
            
            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['ERR']
            );

        } else {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['ID'];
            $packet['status']   = $result['Status'];
            $packet['description']  = Diagnostic::getError($result['Status']);

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "routeCoverage" HTTP call.
     *
     * @param int $msisdn Number to check for coverage
     *
     * @return array
     */
    public function routeCoverage($msisdn)
    {
        $this->request()->msisdn = $msisdn;

        $response = $this->transfer()->execute(
            self::ENDPOINT_ROUTE_COVERAGE, 
            $this->buildPost($this->request())
        );

        $result = $this->extract($response);

        if (isset($result['ERR'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['ERR']
            );

        } else {

            $packet = array();
            $packet['description'] = (string) $result['OK'];
            $packet['charge'] = (float) $result['Charge'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }

    /**
     * The "getMsgCharge" HTTP call.
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function getMessageCharge($apiMsgId)
    {
        $this->request()->apiMsgId = $apiMsgId;

        $response = $this->transfer()->execute(
            self::ENDPOINT_MESSAGE_CHARGE, 
            $this->buildPost($this->request())
        );
        
        $result = $this->extract($response);

        if (isset($result['ERR'])) {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['ERR']
            );

        } else {
            
            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];
            $packet['status']   = $result['status'];            
            $packet['description']  = Diagnostic::getError($result['status']);
            $packet['charge']   = (float) $result['charge'];

            return $this->wrapResponse(Transport::RESULT_SUCCESS, $packet); 
        }
    }
}