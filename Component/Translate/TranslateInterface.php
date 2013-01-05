<?php
namespace Clickatell\Component\Translate;

use Clickatell\Component\Request as Request;

/**
 * This is the Translate interface. The interface ensures
 * that all new Translaters include these functions.
 *
 * @package Clickatell\Component\Translate
 * @author Chris Brand
 */
interface TranslateInterface
{
	/**
	 * Translate function accepts an array and outputs a mixed response
	 * depending on the Translater.
	 *
	 * @param array $response
	 */
	public function translate(array $response);
}