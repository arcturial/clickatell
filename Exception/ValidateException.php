<?php
namespace Clickatell\Exception;

use \Exception as Exception;

/**
 * This is the custom exception handler for Validation exceptions.
 *
 * @uses \Exception
 * @package Clickatell\Exception
 * @author Chris Brand
 */
class ValidateException extends Exception
{
    /**
	 * A number contained a leading zero. (ex. 0215556666 instead of 27215556666)
	 * @var string
	 */
    const ERR_LEADING_ZERO   = "Replace leading 0's with the area code instead. Leading 0's might result in routing errors."; 

    /**
	 * Triggers when an integer...is not an integer
	 * @var string
	 */
    const ERR_INVALID_NUM   = "Integer is invalid. Please ensure you passed a real number."; 
}