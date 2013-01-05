<?php
namespace Clickatell\Exception;

use \Exception as Exception;

/**
 * This is the custom exception handler for Transport exceptions.
 *
 * @uses \Exception
 * @package Clickatell\Exception
 * @author Chris Brand
 */
class TransportException extends Exception
{
	/**
	 * The selected Transport does not implement Clickatell\Component\Transport\TransportInterface.
	 * @var string
	 */
 	const ERR_UNSUPPORTED_TRANSPORT = "Transport selected is not supported.";

 	/**
	 * The selected Transport class file could not be found.
	 * @var string
	 */
    const ERR_TRANSPORT_NOT_FOUND   = "Transport is unavailable. Please ensure your Transport file is correctly located."; 

    /**
	 * The requested method is not available for this Transport.
	 * @var string
	 */
    const ERR_METHOD_NOT_FOUND   = "Method is unavailable for this Transport.";   
}