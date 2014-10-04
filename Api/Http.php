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

/**
 * This is the HTTP interface to Clickatell. It transforms
 * the Request object into a suitable query string and
 * handles the response from Clickatell.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class Http extends Api implements ApiInterface
{

    /**
     * The "sendMsg" HTTP call. Builds up the request and handles the response
     * from Clickatell.
     *
     * @param array   $to       Recipient list
     * @param string  $message  Message
     * @param string  $from     From address (sender ID)
     * @param boolean $callback Use callback or not
     * @param array   $extra    Extra parameters (based on Clickatell documents)
     *
     * @return array
     */
    public function sendMessage(array $to, $message, $from = "", $callback = true, $extra = array())
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Build data packet
        $packet['to'] = implode(",", $to);
        $packet['text'] = $message;
        $packet['from'] = $from;
        $packet['callback'] = $callback;

        $this->extractExtra($extra, $packet);

        $result = $this->callApi('http://api.clickatell.com/http/sendmsg', $packet);

        $result = $this->extract($result, true);
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
     * The "getBalance" HTTP call.
     *
     * @return array
     */
    public function getBalance()
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        $result = $this->callApi(
            'http://api.clickatell.com/http/getbalance',
            $packet
        );

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
     * The "queryMsg" HTTP call.
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

        // Gather packet
        $packet['apiMsgId'] = $apiMsgId;

        $result = $this->callApi('http://api.clickatell.com/http/querymsg', $packet);

        $result = $this->extract($result);

        if (!isset($result['ERR'])) {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['ID'];
            $packet['status'] = $result['Status'];
            $packet['description'] = Diagnostic::getError($result['Status']);

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['ERR']);
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
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather packet
        $packet['msisdn'] = $msisdn;

        $result = $this->callApi(
            'http://api.clickatell.com/utils/routeCoverage',
            $packet
        );

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
     * The "getMsgCharge" HTTP call.
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
        $packet['apiMsgId'] = $apiMsgId;

        $result = $this->callApi(
            'http://api.clickatell.com/http/getmsgcharge',
            $packet
        );

        $result = $this->extract($result);

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