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
<<<<<<< HEAD:Component/Transport/TransportInterface.php
 * @package  Clickatell\Component\Transport
=======
 * @package  Clickatell\Api\Definition
>>>>>>> dev:Api/Definition/ApiInterface.php
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
<<<<<<< HEAD:Component/Transport/TransportInterface.php
namespace Clickatell\Component\Transport;

use Clickatell\Component\Request as Request;
=======
namespace Clickatell\Api\Definition;

>>>>>>> dev:Api/Definition/ApiInterface.php

/**
 * This interface defines the required function for Transport handlers. 
 * It also specifies the supported API calls.
 *
 * @category Clickatell
<<<<<<< HEAD:Component/Transport/TransportInterface.php
 * @package  Clickatell\Component\Transport
=======
 * @package  Clickatell\Api\Definition
>>>>>>> dev:Api/Definition/ApiInterface.php
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
<<<<<<< HEAD:Component/Transport/TransportInterface.php
interface TransportInterface
{
    /**
     * buildPost() formats a suitable request string based on the Request object
     *
     * @param Clickatell\Component\Request $request Request object to process
     *
     * @return mixed
     */
    public function buildPost(Request $request);

    /**
     * API call for "sendMessage".
     *
     * @param string  $to       The recipient list
=======
interface ApiInterface
{
    /**
     * API call for "sendMessage".
     *
     * @param array   $to       The recipient list
>>>>>>> dev:Api/Definition/ApiInterface.php
     * @param string  $message  Message
     * @param string  $from     The from number (sender ID)
     * @param boolean $callback Use the callback or not
     *
     * @return mixed
     */
<<<<<<< HEAD:Component/Transport/TransportInterface.php
    public function sendMessage($to, $message, $from = "", $callback = true);
=======
    public function sendMessage(array $to, $message, $from = "", $callback = true);
>>>>>>> dev:Api/Definition/ApiInterface.php

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