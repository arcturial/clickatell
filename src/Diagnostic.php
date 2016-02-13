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
* This diagnostic file maps Clickatell message response codes to error messages. It's
* a convenience class.
* Version of HTTP API Specification: 2.5.5
*
* @package  Clickatell
* @author   Chris Brand <chris@cainsvault.com>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     https://github.com/arcturial
*/
class Diagnostic
{
    /**
     * @var array
     */
    private static $statusCodes = array(
        "001" => "Authentication failed: Authentication details are incorrect.",
        "002" => "Unknown username or password: Authorization error, unknown user name or incorrect password.",
        "003" => "Session ID expired: The session ID has expired after a pre-set time of inactivity.",
        "005" => "Missing session ID: Missing session ID attribute in request.",
        "007" => "IP Lockdown violation: You have locked down the API instance to a specific IP address and then sent from an IP address different to the one you set.",
        "101" => "Invalid or missing parameters: One or more required parameters are missing or invalid",
        "102" => "Invalid user data header: The format of the user data header is incorrect.",
        "103" => "Unknown API message ID: The API message ID is unknown. Log in to your API account to check the ID or create a new one.",
        "104" => "Unknown client message ID: The client ID message that you are querying does not exist.",
        "105" => "Invalid destination address: The destination address you are attempting to send to is invalid.",
        "106" => "Invalid source address: The sender address that is specified is incorrect.",
        "107" => "Empty message: The message has no content.",
        "108" => "Invalid or missing API ID: The API message ID is either incorrect or has not been included in the API call.",
        "109" => "Missing message ID: This can be either a client message ID or API message ID. For example when using the stop message command.",
        "113" => "Maximum message parts exceeded: The text message component of the message is greater than the permitted 160 characters (70 Unicode characters). Select concat equal to 1,2,3-N to overcome this by splitting the message across multiple messages.",
        "114" => "Cannot route message: This implies that the gateway is not currently routing messages to this network prefix. Please email support@clickatell.com with the mobile number in question.",
        "115" => "Message expired: Message has expired before we were able to deliver it to the upstream gateway. No charge applies.",
        "116" => "Invalid Unicode data: The format of the unicode data entered is incorrect.",
        "120" => "Invalid delivery time: The format of the delivery time entered is incorrect.",
        "121" => "Destination mobile number blocked: This number is not allowed to receive messages from us and has been put on our block list.",
        "122" => "Destination mobile opted out: The user has opted out and is no longer subscribed to your service.",
        "123" => "Invalid Sender ID: A sender ID needs to be registered and approved before it can be successfully used in message sending.",
        "128" => "Number delisted: This error may be returned when a number has been delisted.",
        "130" => "Maximum MT limit exceeded until <UNIX TIME STAMP>: This error is returned when an account has exceeded the maximum number of MT messages which can be sent daily or monthly. You can send messages again on the date indicated by the UNIX TIMESTAMP.",
        "201" => "Invalid batch ID: The batch ID which you have entered for batch messaging is not valid.",
        "202" => "No batch template: The batch template has not been defined for the batch command.",
        "301" => "No credit left: Insufficient credits.",
        "901" => "Internal error: Please retry.",
    );

    /**
     * Retrieves a description from a specfied code.
     *
     * @param string $code Error code received from API
     *
     * @return string
     */
    public static function getStatus($code)
    {
        return isset(self::$statusCodes[$code]) ? self::$statusCodes[$code] : "unknown error";
    }
}
