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
use \SimpleXMLIterator as SimpleXMLIterator;

/**
 * This is the XML interface to Clickatell. It transforms
 * the Request object into a suitable query string and
 * handles the response from Clickatell. The request
 * is transformed into a xml packet.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class Xml extends Api implements ApiInterface
{
    /**
     * XML endpoint
     * @var string
     */
    const XML_ENDPOINT = "http://api.clickatell.com/xml/xml";

    /**
     * {@inheritdoc}
     */
    protected function extract($response, $multi = false)
    {
        $iterator = new SimpleXMLIterator($response);
        $iterator->rewind();

        $result = array();

        foreach ($iterator as $entry) {
            $row = array();
            foreach ($entry as $elementName => $node) {
                $string = preg_replace("/To:(.*)/", "", (string) $node);
                $row[$elementName] = trim($string);
            }

            $result[] = $row;
        }

        return $multi ? $result : current($result);
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
        $packet['to'] = implode(",", $to);
        $packet['text'] = $message;
        $packet['from'] = $from;
        $packet['callback'] = $callback;

        $this->extractExtra($extra, $packet);

        $xmlPacket['clickAPI']['sendMsg'] = $packet;

        $result = $this->callApi(
            self::XML_ENDPOINT,
            array('data' => Utility::arrayToString($xmlPacket))
        );

        $result = $this->extract($result, true);
        $error = false;
        $return = array();

        foreach ($result as $row) {
            if (isset($row['fault'])) {
                $error = true;
            }

            $return[] = array(
                'apiMsgId' => (isset($row['apiMsgId'])) ? $row['apiMsgId'] : false,
                'to' => (isset($row['to'])) ? $row['to'] : $packet['to'],
                'error' => (isset($row['fault'])) ? $row['fault'] : false
            );
        }

        if (!$error) {
            return $this->wrapResponse(Api::RESULT_SUCCESS, $return);
        } else {
            return $this->wrapResponse(Api::RESULT_FAILURE, $return);
        }
    }

    /**
     * The "getBalance" XML call.
     *
     * @return array
     */
    public function getBalance()
    {
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        $xmlPacket['clickAPI']['getBalance'] = $packet;

        $result = $this->callApi(
            self::XML_ENDPOINT,
            array('data' => Utility::arrayToString($xmlPacket))
        );

        $result = $this->extract($result);

        if (!isset($result['fault'])) {

            $packet = array();
            $packet['balance'] = (float) $result['ok'];

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['fault']);
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
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather packet
        $packet['apiMsgId'] = $apiMsgId;

        $xmlPacket['clickAPI']['queryMsg'] = $packet;

        $result = $this->callApi(
            self::XML_ENDPOINT,
            array('data' => Utility::arrayToString($xmlPacket))
        );

        $result = $this->extract($result);

        if (!isset($result['fault'])) {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];
            $packet['status'] = trim((string) $result['status']);
            $packet['description'] = Diagnostic::getError($packet['status']);

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['fault']);
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
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather packet
        $packet['msisdn'] = $msisdn;

        $xmlPacket['clickAPI']['routeCoverage'] = $packet;

        $result = $this->callApi(
            self::XML_ENDPOINT,
            array('data' => Utility::arrayToString($xmlPacket))
        );

        $result = $this->extract($result);

        if (!isset($result['fault'])) {

            $packet = array();
            $packet['description'] = (string) $result['ok'];
            $packet['charge'] = (float) $result['charge'];

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['fault']);
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
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];
        $packet['password'] = $this->auth['password'];
        $packet['api_id'] = $this->auth['api_id'];

        // Gather packet
        $packet['apiMsgId'] = $apiMsgId;

        $xmlPacket['clickAPI']['getMsgCharge'] = $packet;

        $result = $this->callApi(
            self::XML_ENDPOINT,
            array('data' => Utility::arrayToString($xmlPacket))
        );

        $result = $this->extract($result);

        if (!isset($result['fault'])) {

            $packet = array();
            $packet['apiMsgId'] = (string) $result['apiMsgId'];
            $packet['status'] = trim((string) $result['status']);
            $packet['description'] = Diagnostic::getError($result['status']);
            $packet['charge'] = (float) $result['charge'];

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet);

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['fault']);
        }
    }
}