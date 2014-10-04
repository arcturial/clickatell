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
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Api;

use Clickatell\Api\Definition\ApiInterface as ApiInterface;
use Clickatell\Exception\Diagnostic as Diagnostic;
use Clickatell\Component\Utility as Utility;
use Clickatell\Exception\TransferException as TransferException;
use \SoapClient as SoapClient;

/**
 * This is the SOAP interface to Clickatell. It transforms
 * the Request object into a SOAP request.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class Soap extends Api implements ApiInterface
{
    /**
     * Clickatell WSDL endpoint
     * @var string
     */
    const SOAP_ENDPOINT = "http://api.clickatell.com/soap/webservice.php?WSDL";

    /**
     * Build the parameter list by combining the list in the SOAP
     * WSDL and the parameters required.
     *
     * @param \SoapClient $soap   The soap client
     * @param string      $action The function name
     * @param array       $param  The parameters passed by user
     *
     * @return array
     */
    private function _refreshParameterList(SoapClient $soap, $action, array $param)
    {
        $paramList = $soap->__getFunctions();
        $result = array();

        // Match the action
        foreach ($paramList as $key => $value) {

            if (strpos($value, $action) !== false) {

                // Matched
                preg_match_all("/\\$([a-z_]*)/", $value, $matches);

                $result = $matches[1];
                $result = array_flip($result);
                break;
            }
        }

        foreach ($result as $key => $value) {

            $result[$key] = isset($param[$key]) ? $param[$key] : '';
        }

        return $result;
    }

    /**
     * Do a SOAP request to the Clickatell API
     *
     * @param string $url    URL to call
     * @param array  $packet Packet to process
     *
     * @return string
     */
    protected function callApi($url, array $packet)
    {
        // Check if SOAP exists
        if (!class_exists('\SoapClient')) {
            throw new TransferException(TransferException::ERR_SOAP_DISABLED);
        }

        try {

            $soap = new SoapClient(
                self::SOAP_ENDPOINT,
                array("exceptions" => 1, "trace" => 1)
            );

            $packet = $this->_refreshParameterList($soap, $url, $packet);

            return $soap->__soapCall($url, $packet);

        } catch (Exception $exception) {

            throw new TransferException(TransferException::ERR_HANLDER_EXCEPTION);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage(array $to, $message, $from = "", $callback = true, $extra = array())
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Build data packet
        $packet['to'] = $to;
        $packet['text'] = $message;
        $packet['from'] = $from;
        $packet['callback'] = $callback;

        $this->extractExtra($extra, $packet);

        $result = $this->callApi('sendmsg', $packet);
        $result = $this->extract(implode("\n", $result), true);
        $error = false;
        $return = array();

        foreach ($result as $row) {
            if (isset($row['ERR'])) {
                $error = true;
            }

            $return[] = array(
                'apiMsgId' => (isset($row['ID'])) ? $row['ID'] : false,
                'to' => (isset($row['To'])) ? $row['To'] : $packet['to'],
                'error' => (isset($row['ERR'])) ? $row['ERR'] : false
            );
        }

        if (!$error) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $return);
        } else {
            return $this->wrapResponse(Api::RESULT_FAILURE, $return);
        }
    }

    /**
     * The "getBalance" SOAP call.
     *
     * @return array
     */
    public function getBalance()
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        $result = $this->callApi('getbalance', $packet);

        $result = $this->extract($result);

        if (!isset($result['ERR'])) {

            $packet = array();
            $packet['balance'] = (float) $result['Credit'];

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['ERR']);
        }
    }

    /**
     * The "queryMsg" SOAP call.
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function queryMessage($apiMsgId)
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather data
        $packet['apimsgid'] = $apiMsgId;

        $result = $this->callApi('querymsg', $packet);

        $result = $this->extract(array_shift($result));

        if (!isset($result['ERR'])) {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['ID'];
            $packet['status']   = $result['Status'];
            $packet['description']  = Diagnostic::getError($result['Status']);

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['ERR']);
        }
    }

    /**
     * The "routeCoverage" SOAP call.
     *
     * @param int $msisdn Number to check for coverage
     *
     * @return array
     */
    public function routeCoverage($msisdn)
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather packet
        $packet['msisdn'] = $msisdn;

        $result = $this->callApi('routeCoverage', $packet);

        $result = $this->extract($result);

        if (!isset($result['ERR'])) {

            $packet = array();
            $packet['description'] = (string) $result['OK'];
            $packet['charge'] = (float) $result['Charge'];

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['ERR']);
        }
    }

    /**
     * The "getMsgCharge" SOAP call.
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function getMessageCharge($apiMsgId)
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather packet
        $packet['apimsgid'] = $apiMsgId;

        $result = $this->callApi('getmsgcharge', $packet);

        $result = $this->extract(array_shift($result));

        if (!isset($result['ERR'])) {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];
            $packet['status']   = $result['status'];
            $packet['description']  = Diagnostic::getError($result['status']);
            $packet['charge']   = (float) $result['charge'];

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['ERR']);
        }
    }
}