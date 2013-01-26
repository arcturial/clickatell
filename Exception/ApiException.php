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
<<<<<<< HEAD:Component/Transport/TransportInterfaceExtract.php
 * @package  Clickatell\Component\Transport
=======
 * @package  Clickatell\Exception
>>>>>>> dev:Exception/ApiException.php
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
<<<<<<< HEAD:Component/Transport/TransportInterfaceExtract.php
namespace Clickatell\Component\Transport;

/**
 * The extracting interface just ensures a class is capable of performing
 * a result extraction from the API call return.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface TransportInterfaceExtract
{
    /**
     * Extracts the result from the API into an associative array
     *
     * @param mixed $response Response string from API
     *
     * @return array
     */
    public function extract($response);
=======
namespace Clickatell\Exception;

use \Exception as Exception;

/**
 * This is the custom exception handler for API exceptions.
 *
 * @category Clickatell
 * @package  Clickatell\Exception
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     \Exception
 */
class ApiException extends Exception
{
    /**
     * The API does not contain this method.
     * @var string
     */
    const ERR_METHOD_NOT_FOUND = "This method does not exist as part of this API.";  
>>>>>>> dev:Exception/ApiException.php
}