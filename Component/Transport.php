<?php
namespace Clickatell\Component;

use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Component\Transfer\TransferInterface as TransferInterface;
use Clickatell\Component\Request as Request;

/**
 * This is an abstraction of the Transport class. It contains some default
 * setup an utility functions.
 *
 * @abstract
 * @package Clickatell\Component
 * @author Chris Brand
 */
abstract class Transport implements TransportInterface
{
	/**
	 * Failed API Response
	 * @var string
	 */
	const RESULT_FAILURE = "failure";

	/**
	 * Successful API Response
	 * @var string
	 */
	const RESULT_SUCCESS = "success";

	/**
	 * The Transport object associated with this Transport.
	 * @var Clickatell\Component\Transport\TransferInterface
	 */
	private $_transfer;

	/**
	 * The Request object associated with this Transport.
	 * @var Clickatell\Component\Request
	 */
	private $_request;

	/**
	 * Instantiation of the Transport. Saves the Transfer and Request objects
	 * in the Transport.
	 *
	 * @param Clickatell\Component\Transfer\TransferInterface $transfer
	 * @param Clickatell\Component\Request $request
	 * @return boolean
	 */
	public function __construct(TransferInterface $transfer, Request $request)
	{
		$this->_transfer = $transfer;
		$this->_request = $request;
	}

	/**
	 * Returns the Request object associated with the Transport.
	 * 
	 * @return Clickatell\Component\Request
	 */
	public function request()
	{
		return $this->_request;
	}

	/**
	 * Returns the Transfer object associated with the Transport. This
	 * can be changed via the Messenger class.
	 *
	 * @see Clickatell/Clickatell.php
	 * @return Clickatell\Component\Transfer\TransferInterface
	 */
	public function transfer()
	{
		return $this->_transfer;
	}

	/**
	 * Utility function to wrap the response from the API in generic
	 * array format that can be translate by the Translater.
	 *
	 * @param strng $status
	 * @param string $response
	 * @return array
	 */
	public function wrapResponse($status, $response)
	{
		$result = array(
			"result" => array(
					"status" => $status
				)
			);

		$result['result']['response'] = $response;

		return $result;
	}
}