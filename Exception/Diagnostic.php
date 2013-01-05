<?php
namespace Clickatell\Exception;

/**
 * This diagnostic file maps Clickatell response codes to error messages. It's
 * a convenience class.
 *
 * @package Clickatell\Exception
 * @author Chris Brand
 */
class Diagnostic
{	
	/**
	 * Array of message status codes
	 * @var array
	 */
	private static $_errorCodes = array(
	    	"001" => "The message ID is incorrect or reporting is delayed.",
	    	"002" => "The message could not be delivered and has been queued for attempted redelivery.",
	    	"003" => "Delivered to the upstream gateway or network (delivered to the recipient).",
	    	"004" => "Confirmation of receipt on the handset of the recipient.",
	    	"005" => "There was an error with the message, probably caused by the content of the message itself.",
	    	"006" => "The message was terminated by a user (stop message command) or by our staff.",
	    	"007" => "An error occurred delivering the message to the handset. 008 0x008 OK Message received by gateway.",
	    	"009" => "The routing gateway or network has had an error routing the message.",
	    	"010" => "Message has expired before we were able to deliver it to the upstream gateway. No charge applies.",
	    	"011" => "Message has been queued at the gateway for delivery at a later time (delayed delivery).",
	    	"012" => "The message cannot be delivered due to a lack of funds in your account. Please re-purchase credits.",
	    	"014" => "Maximum MT limit exceeded The allowable amount for MT messaging has been exceeded.",
	    );
	
	/**
	 * Retrieves a description from a specfied code.
	 *
	 * @param string $code
	 * @return string
	 */
	public static function getError($code)
	{
		return isset(self::$_errorCodes[$code]) ? self::$_errorCodes[$code] : "";
	}
}