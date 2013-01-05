<?php
namespace Clickatell\Component\Transport;

/**
 * The extracting interface just ensures a class is capable of performing
 * a result extraction from the API call return.
 *
 * @package Clickatell\Component\Transport
 * @author Chris Brand
 */
interface TransportInterfaceExtract
{   
	/**
	 * Extracts the result from the API into an associative array
	 *
	 * @param mixed $response
	 * @return array
	 */
	public function extract($response);
}