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

namespace Clickatell;

/**
 * This interface defines the required function for Transport handlers.
 * It also specifies the supported API calls.
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface TransportInterface
{
    /**
     * API call for "sendMessage".
     *
     * @param array   $to       The recipient list
     * @param string  $message  Message
     * @param array   $extra    Extra parameters (based on Clickatell documents)
     *
     * @return mixed
     */
    public function sendMessage($to, $message, $extra = array());

    /**
     * API call for "getBalance".
     *
     * @return mixed
     */
    public function getBalance();

    /**
     * API call for "stop message".
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return mixed
     */
    //public function stopMessage($apiMsgId);

    /**
     * API call for "queryMsg".
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return mixed
     */
    //public function queryMessage($apiMsgId);

    /**
     * API call for "routeCoverage".
     *
     * @param int $msisdn Number to check for coverage
     *
     * @return mixed
     */
    //public function routeCoverage($msisdn);

    /**
     * API call for "getMsgCharge".
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return mixed
     */
    //public function getMessageCharge($apiMsgId);
}