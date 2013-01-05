<?php
namespace Clickatell\Component\Translate;

use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;

/**
 * This is the JSON Translater. It takes a response from the Transport
 * and translates them into your requested format. In this case...JSON
 *
 * @package Clickatell\Component\Translate
 * @author Chris Brand
 */
class TranslateJson implements TranslateInterface
{
	/**
	 * Translates an array to JSON
	 *
	 * @param array $response
	 * @return string
	 */
	public function translate(array $response)
	{
		return json_encode($response);
	}
}