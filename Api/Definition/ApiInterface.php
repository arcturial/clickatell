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
 * @package  Clickatell\Api\Definition
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Api\Definition;


/**
 * This interface defines the required function for Transport handlers. 
 * It also specifies the supported API calls.
 *
 * @category Clickatell
 * @package  Clickatell\Api\Definition
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface ApiInterface
{
    /**
     * API call for "sendMessage".
     *
     * @param array   $to       The recipient list
     * @param string  $message  Message
     * @param string  $from     The from number (sender ID)
     * @param boolean $callback Use the callback or not
     *
     * @return mixed
     */
    public function sendMessage(array $to, $message, $from = "", $callback = true);

    /**
     * API call for "getBalance".
     *
     * @return mixed
     */
    public function getBalance();

    /**
     * API call for "queryMsg".
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return mixed
     */
    public function queryMessage($apiMsgId);

    /**
     * API call for "routeCoverage".
     *
     * @param int $msisdn Number to check for coverage
     *
     * @return mixed     
     */
    public function routeCoverage($msisdn);

    /**
     * API call for "getMsgCharge".
     *
     * @param string $apiMsgId ApiMsgId to query
     *
     * @return mixed
     */
    public function getMessageCharge($apiMsgId);
}