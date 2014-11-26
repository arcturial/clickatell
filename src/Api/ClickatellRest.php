<?php
/**
 * The Clickatell SMS Library provides a standardised way of talking to and
 * receiving replies from the Clickatell API's.
 *
 * PHP Version 5.3
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */

namespace Clickatell\Api;

use Clickatell\Clickatell;
use Clickatell\Response\SendMessage;
use \Exception;

/**
 * The REST API usage class.
 *
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ClickatellRest extends Clickatell
{
    private $token;

    /**
     * Construct a new REST API connection.
     *
     * @param string $token The auth token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Return the headers that will be used with every REST
     * request.
     *
     * @return array
     */
    private function getHeaders()
    {
        return array(
            "Authorization: Bearer " . $this->token,
            "Content-Type: application/json",
            "X-Version: 1",
            "Accept: application/json"
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function get($uri, $args, $method = self::HTTP_GET)
    {
        $data = json_encode($args);
        $response = $this->curl($uri, $data, $this->getHeaders(), $method);
        $decoded = $response->decodeRest();

        // Check if the decoded response contains a "global error". If the entire
        // packet failed there is no need to even try handling it further.
        if (isset($decoded['error'])) {
            // The assumption here is that every response will behave the same and when it's an
            // error it will always contain a description and code field.
            throw new Exception($decoded['error']['description'], $decoded['error']['code']);
        }

        return $decoded['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage($to, $message, $extra = array())
    {
        $extra['to'] = (array) $to;
        $extra['text'] = $message;
        $args = $this->getSendDefaults($extra);

        // The "to" field only accepts strings as numbers. We will take all the
        // values and map them into strings.
        $args['to'] = array_map(function ($value) {
            return (string) $value;
        }, $args['to']);

        $response = $this->get('rest/message', $args, self::HTTP_POST);
        $return = array();

        // According to the documentation, we can pretty much assume that
        // a response from "rest/message" will contain a "message" key with an
        // array of messages in it.
        foreach ($response['message'] as $entry) {

            $return[] = new SendMessage(
                (isset($entry['apiMessageId'])) ? $entry['apiMessageId'] : false,
                (isset($entry['to'])) ? $entry['to'] : $args['to'],
                (isset($entry['error'])) ? $entry['error']['description'] : false,
                (isset($entry['error'])) ? $entry['error']['code'] : false
            );
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        throw new Exception('Get balance functionality not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function queryMessage($apiMsgId)
    {
        throw new Exception('Query message functionality not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function routeCoverage($msisdn)
    {
        throw new Exception('Route coverage functionality not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCharge($apiMsgId)
    {
        throw new Exception('Get message charge functionality not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function stopMessage($apiMsgId)
    {
        throw new Exception('Stop message functionality not implemented yet.');
    }
}