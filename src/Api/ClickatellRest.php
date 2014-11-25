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
use \stdClass;
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
            "Authorization" => "Bearer " . $this->token,
            "Content-Type"  => "application/json",
            "X-Version"     => "1",
            "Accept"        => "application/json"
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function get($uri, $args, $method = self::HTTP_GET)
    {
        $response = $this->curl($uri, $args, $this->getHeaders(), $method);
        $decoded = json_decode($response, true);


        return $decoded['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage($to, $message, $extra = array())
    {
        // Merge parameter sets and include some
        // default parameters.
        // TODO abstrac this, it's shared between HTTP and REST
        $args = array_merge(
            array(
                'to'        => implode(",", (array) $to),
                'text'      => $message,
                'mo'        => true,
                'callback'  => true
            ),
            $extra
        );

        $response = $this->get('rest/message', $args, self::HTTP_POST);
        $return = array();

        foreach ($response['message'] as $entry) {
            $obj = new stdClass;
            $obj->id = (isset($entry['apiMessageId'])) ? $entry['apiMessageId'] : false;
            $obj->to = (isset($entry['to'])) ? $entry['to'] : $args['to'];
            $obj->errorCode = (isset($entry['code'])) ? $entry['code'] : false;
            $obj->error = (isset($entry['error'])) ? $entry['error'] : false;
            $return[] = $obj;
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