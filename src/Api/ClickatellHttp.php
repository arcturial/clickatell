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
use \stdClass;
use \Exception;

/**
 * The HTTP API usage class.
 *
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ClickatellHttp extends Clickatell
{
    private $username;
    private $password;
    private $apiId;

    /**
     * Construct a new HTTP API connection.
     *
     * @param string $username The username
     * @param string $password The password
     * @param int    $apiId    The clickatell API ID
     */
    public function __construct($username, $password, $apiId)
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiId = $apiId;
    }

    /**
     * {@inheritdoc}
     */
    protected function get($uri, $args, $method = self::HTTP_GET)
    {
        $args = array_merge(
            $args,
            array(
                'user'      => $this->username,
                'password'  => $this->password,
                'api_id'    => $this->apiId
            )
        );

        $query = http_build_query($args);
        return $this->curl($uri, $query, array(), $method);
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage($to, $message, $extra = array())
    {
        $extra['to'] = implode(",", (array) $to);
        $extra['text'] = $message;
        $args = $this->getSendDefaults($extra);

        $response = $this->get('http/sendmsg', $args);
        $return = array();

        // We won't throw any exceptions if an error occurs since we could have
        // multiple messages in the packet and not all of them might have failed.
        foreach ($response->unwrapLegacy(true) as $entry) {

            $return[] = new SendMessage(
                (isset($entry['ID'])) ? $entry['ID'] : false,
                (isset($entry['To'])) ? $entry['To'] : $args['to'],
                (isset($entry['error'])) ? $entry['error'] : false,
                (isset($entry['code'])) ? $entry['code'] : false
            );
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        $response = $this->get('http/getbalance', array());
        $result = $response->unwrapLegacy(false);

        $obj = new stdClass;
        $obj->balance = (float) $result['Credit'];
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function queryMessage($apiMsgId)
    {
        return $this->getMessageCharge($apiMsgId);
    }

    /**
     * {@inheritdoc}
     */
    public function routeCoverage($msisdn)
    {
        $args = array(
            'msisdn' => $msisdn
        );

        $response = $this->get('utils/routeCoverage', $args);
        $result = $response->unwrapLegacy(false);

        $obj = new stdClass;
        $obj->apiMsgId = (string) $result['OK'];
        $obj->charge = $result['Charge'];
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCharge($apiMsgId)
    {
        $args = array(
            'apiMsgId' => $apiMsgId
        );

        $response = $this->get('http/getmsgcharge', $args);
        $result = $response->unwrapLegacy(false);

        $obj = new stdClass;
        $obj->status = $result['status'];
        $obj->description = Diagnostic::getError($result['status']);
        $obj->charge = (float) $result['charge'];
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function stopMessage($apiMsgId)
    {
        throw new Exception('Stop message functionality not implemented yet.');
    }
}