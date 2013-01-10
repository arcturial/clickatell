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
 * This is the custom exception handler for Transport exceptions.
 *
 * @category Clickatell
 * @package  Clickatell\Exception
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     \Exception
 */
class TransportException extends Exception
{
    /**
     * The selected Transport does not 
     * implement Clickatell\Component\Transport\TransportInterface.
     * @var string
     */
    const ERR_UNSUPPORTED_TRANSPORT = "Transport selected is not supported.";

    /**
     * The selected Transport class file could not be found.
     * @var string
     */
    const ERR_TRANSPORT_NOT_FOUND = "Transport is unavailable. Please ensure your Transport file is correctly located."; 

    /**
     * The requested method is not available for this Transport.
     * @var string
     */
    const ERR_METHOD_NOT_FOUND = "Method is unavailable for this Transport.";   
}