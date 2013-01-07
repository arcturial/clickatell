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
 * This is the custom exception handler for Transfer exceptions.
 *
 * @category Clickatell
 * @package  Clickatell\Exception
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     \Exception
 */
class TransferException extends Exception
{
    /**
     * The Transfer handler encountered a problem.
     * @var string
     */
    const ERR_HANLDER_EXCEPTION   = "Handler Encountered a Problem";   
}