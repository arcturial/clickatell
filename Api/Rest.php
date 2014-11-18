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
 * @author   Tamás Petró, Ennosol Co. Ltd. <tamas.petro@ennosol.eu>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Api;

use Clickatell\Api\Definition\ApiInterface as ApiInterface;
use Clickatell\Exception\Diagnostic as Diagnostic;
use Clickatell\Exception\TransferException as TransferException;
use \InvalidArgumentException;

/**
 * This is the HTTP interface to Clickatell. It transforms
 * the Request object into a suitable query string and
 * handles the response from Clickatell.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Tamás Petró, Ennosol Co. Ltd. <tamas.petro@ennosol.eu>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class Rest extends Api implements ApiInterface
{
    /**
     * The "sendMsg" REST call. Builds up the request and handles the response
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
        // Build data packet
        $packet['to'] = $to;
        $packet['text'] = $message;
        $packet['from'] = $from;
        $packet['callback'] = $callback;

        $this->extractExtra($extra, $packet);

        $result = $this->restPost('https://api.clickatell.com/rest/message', $packet, true);

        if (isset($result['error'])) {
            return $this->wrapResponse(Api::RESULT_FAILURE, $result);
        } else if (isset($result['data'])) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $result);
        }
    }

    /**
     * Extract array keys in the $extra parameters to the an
     * array for REST API.
     *
     * @param array $extra  The extra parameters
     * @param array $packet The packet to send to the API
     *
     * @return boolean
     */
    protected function extractExtra($extra, &$packet)
    {
        foreach ($extra as $key => $value) {
            if (array_key_exists($key, $this->_supportedExtra)) {
                $packet[$this->_supportedExtra[$key]] = $value;
            } else {
                throw new InvalidArgumentException(
                    '"' . $key . '" parameter not supported. (supported: '
                    . implode(",", $this->_supportedExtra) . ')'
                );
            }
        }
    }

    /**
     * Transports needs a way to extract data from the API call. Most
     * API calls sort of have the same response. So this serves as the generic
     * extracter. It can be overwritten for custom Transports.
     *
     * @param string  $response Response from API
     * @param boolean $multi    Should this result return an array of values
     *
     * @return array
     */
    protected function extract($response, $multi = false)
    {
        return json_decode($response, true);
    }

    /**
     * Supported 'extra' parameters for REST API
     * @var array
     */
    private $_supportedExtra = array(
        'unicode' => 'unicode',
        'binary' => 'binary',
        'gateway_escalation' => 'escalate',
        'user_priority_queue' => 'userPriorityQueue',
        'client_message_id' => 'clientMessageId',
        'max_credits' => 'maxCredits',
        'required_features' => 'requiredFeatures',
        'concatenation' => 'maxMessageParts',
        'delivery_time' => 'scheduledDeliveryTime',
        'udh' => 'udh',
        'validity_period' => 'validityPeriod',
        'type' => 'type',
        'two-way_messaging' => 'mo'
    );

    /**
     * Build header string
     *
     * @return string
     */
    private function _buildHeader()
    {
        $header = array(
            'X-Version: 1',
            'Authorization: Bearer ' . $this->auth['token'],
            'Content-Type: application/json',
            'Accept: application/json'
        );

        return $header;
    }

    /**
     * Curl init
     *
     * @param string  $url      URL to call
     *
     */
    private function _makeCurl($url)
    {
        // Check if we have CURL
        if (!function_exists('curl_init')) {
            throw new TransferException(TransferException::ERR_CURL_DISABLED);
        }

        $header = $this->_buildHeader();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_sslVerification);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->_sslVerification);

        return $ch;
    }

    /**
     * Do a CURL request to call the API with POST method.
     *
     * @param string  $url      URL to call
     * @param array   $packet   Packet to process
     *
     * @return string
     */
    protected function restPost($url, array $packet)
    {
        $ch = $this->_makeCurl($url);
        $post = json_encode($packet);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = json_decode(curl_exec($ch), true);
        $result['httpStatus'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if the call completed
        if (!curl_errno($ch)) {
            return $result;
        } else {
            throw new TransferException(TransferException::ERR_HANLDER_EXCEPTION);
        }
    }

    /**
     * Do a CURL request to call a REST endpoint with GET method.
     *
     * @param string  $url      URL to call
     *
     * @return string
     */
    private function _restGet($url) {
        $ch = $this->_makeCurl($url);

        $result = json_decode(curl_exec($ch), true);
        $result['httpStatus'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if the call completed
        if (!curl_errno($ch)) {
            return $result;
        } else {
            throw new TransferException(TransferException::ERR_HANLDER_EXCEPTION);
        }
    }

    /**
     * Do a CURL request to call a REST endpoint with DELETE method.
     *
     * @param string  $url      URL to call
     *
     * @return string
     */
    private function _restDelete($url) {
        $ch = $this->_makeCurl($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $result = json_decode(curl_exec($ch), true);
        $result['httpStatus'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if the call completed
        if (!curl_errno($ch)) {
            return $result;
        } else {
            throw new TransferException(TransferException::ERR_HANLDER_EXCEPTION);
        }
    }

    /**
     * Get Account Balance
     *
     * @return array
     */
    public function getBalance()
    {
        $result = $this->_restGet('https://api.clickatell.com/rest/account/balance');

        if (!isset($result['error'])) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $result);
        } else {
            return $this->wrapResponse(Api::RESULT_FAILURE, $result);
        }
    }

    /**
     * Stop a message
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function stopMessage($apiMsgId)
    {
        $result = $this->_restDelete('https://api.clickatell.com/rest/message/' . $apiMsgId);

        if (!isset($result['error'])) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $result);
        } else {
            return $this->wrapResponse(Api::RESULT_FAILURE, $result);
        }
    }

    /**
     * Query the status of a message
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return array
     */
    public function queryMessage($apiMsgId)
    {
        $result = $this->_restGet('https://api.clickatell.com/rest/message/' . $apiMsgId);

        if (!isset($result['error'])) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $result);
        } else {
            return $this->wrapResponse(Api::RESULT_FAILURE, $result);
        }
    }

    /**
     * Get Coverage Information
     *
     * @param int $msisdn Number (mobile number) to check for coverage
     *
     * @return array
     */
    public function routeCoverage($msisdn)
    {
        $result = $this->_restGet('https://api.clickatell.com/rest/coverage/' . $msisdn);

        if (!isset($result['error'])) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $result);
        } else {
            return $this->wrapResponse(Api::RESULT_FAILURE, $result);
        }
    }

    /**
     * API call for "getMsgCharge" - Not available with REST API
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     */
    public function getMessageCharge($apiMsgId) {}
}
