<?php
/**
 * The Clickatell SMS Library provides a standardised way of talking to and
 * receiving replies from the Clickatell API's. It makes it
 * easier to write your applications and grants the ability to
 * quickly switch the type of API you want to use HTTP/XML without
 * changing any code.
 *
 * PHP Version 5.3
 *
 * @category Clickatell
 * @package  Clickatell\Exception
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Exception;

use \Exception as Exception;

/**
 * This is the custom exception handler for Validation exceptions.
 *
 * @category Clickatell
 * @package  Clickatell\Exception
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     \Exception
 */
class ValidateException extends Exception
{
    /**
     * A number contained a leading zero. (ex. 0215556666 instead of 27215556666)
     * @var string
     */
    const ERR_LEADING_ZERO = "Replace leading 0's with the area code instead. Leading 0's might result in routing errors."; 

    /**
     * Triggers when an integer...is not an integer
     * @var string
     */
    const ERR_INVALID_NUM = "Integer is invalid. Please ensure you passed a real number."; 
}