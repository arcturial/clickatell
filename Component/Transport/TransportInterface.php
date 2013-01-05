<?php
namespace Clickatell\Component\Transport;

use Clickatell\Component\Request as Request;

/**
 * This interface defines the required function for Transport handlers. 
 * It also specifies the supported API calls.
 *
 * @package Clickatell\Component\Transport
 * @author Chris Brand
 */
interface TransportInterface
{   
	/**
	 * buildPost() formats a suitable request string based on the Request object
	 *
	 * @param Clickatell\Component\Request $request
	 */
	public function buildPost(Request $request);

	/**
	 * API call for "sendMessage".
	 *
	 * @param string $to
	 * @param string $message
	 * @param string $from
	 * @param boolean $callback
	 */
	public function sendMessage($to, $message, $from = "", $callback = true);

	/**
	 * API call for "getBalance".
	 */
    public function getBalance();

    /**
	 * API call for "queryMsg".
	 *
	 * @param string $apiMsgId
	 */
    public function queryMessage($apiMsgId);

    /**
	 * API call for "routeCoverage".
	 *
	 * @param int $msisdn
	 */
    public function routeCoverage($msisdn);

    /**
	 * API call for "getMsgCharge".
	 *
	 * @param string $apiMsgId
	 */
    public function getMessageCharge($apiMsgId);
}