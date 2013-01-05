<?php
namespace Clickatell\Component\Transfer;

use Clickatell\Component\Request as Request;

/**
 * This is the Transfer interface. Any new Transfer handlers need to
 * implement this to ensure that functionality stays in tact.
 *
 * @package Clickatell\Component\Transfer
 * @author Chris Brand
 */
interface TransferInterface
{   
	/**
	 * Executes the Transfer interface.
	 *
	 * @param string $url
	 * @param array $param
	 * @return string
	 */
	public function execute($url, $param);
}