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
 * It also specifies the supported API calls for bulk messaging processing.
 *
 * @category Clickatell
 * @package  Clickatell\Api\Definition
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface ApiBulkInterface
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
}