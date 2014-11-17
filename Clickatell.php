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
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell;

use Clickatell\Component\Translate\TranslateJson as TranslateJson;
use Clickatell\Api\Api as Api;
use Clickatell\Exception\ApiException as ApiException;
use \Closure as Closure;
use Clickatell\Component\Event as Event;
use Clickatell\Component\Validate as Validate;
use \LogicException;

/**
 * This is the main messenger class that encapsulates various objects to succesfully
 * send Clickatell calls and respond in an appropriate manner. The messenger class
 * enables you to set your own Transport and Translate interfaces.
 *
 * @category Clickatell
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class Clickatell
{
    /**
     * The HTTP Transport Interface
     * @var string
     */
    const HTTP_API = "Clickatell\Api\Http";

    /**
     * The XML Transport Interface
     * @var string
     */
    const XML_API = "Clickatell\Api\Xml";

    /**
     * The SOAP Transport Interface
     * @var string
     */
    const SOAP_API = "Clickatell\Api\Soap";

    /**
     * The SMTP Transport Interface
     * @var string
     */
    const SMTP_API = "Clickatell\Api\Smtp";

    /**
     * The REST Transport Interface
     * @var string
     */
    const REST_API = "Clickatell\Api\Rest";

    /**
     * The transport/api to use for the request
     * @var Clickatell\Api\Api
     */
    private $_transport;


    /**
     * Clickatell Messenger Instantiation. Creates the Transport/Translate/Request
     * interfaces required.
     *
     * @param string $transport Transport protocol to use (defaults to HTTP api)
     *
     * @return boolean
     */
    public function __construct($transport = self::HTTP_API)
    {
        // Register autoloader
        spl_autoload_register(array($this, '_autoLoad'));

        // Create transport
        $this->_transport = new $transport(new TranslateJson());

        // Clear all registered events
        Event::clear();

        // Add validation listener using events
        Event::on(
            'request',
            function ($data) {

                $method = $data['call'];
                $args = $data['request'];

                Validate::processValidation($method, $args);
            }
        );
    }

    /**
     * Clickatell Messenger Authentication with user data.
     *
     * @param string $username  API username
     * @param string $password  API password
     * @param int    $apiId     API ID (Sub-product ID)
     *
     * @return boolean
     */
    public function authUser($username, $password, $apiId) {
        $this->_transport->authenticateUser($username, $password, $apiId);
    }

    /**
     * Clickatell Messenger Authentication with API token.
     *
     * @param string $token API token
     *
     * @return boolean
     */
    public function authToken($token) {
        $this->_transport->authenticateByToken($token);
    }

    /**
     * Setting up SSL verification (turn off on local testing)
     * Use '0' for turn off and '2' for turn on the SSL verification
     *
     * @param int  $setValue   sslVerification value
     */
    public function setSSLVerification($setValue)
    {
        $this->_transport->_sslVerification = $setValue;
    }

    /**
     * Module autoloader. This allows us to integrate our module
     * with other frameworks without clashing.
     *
     * @param string $class Class to load
     *
     * @return boolean
     */
    private function _autoLoad($class)
    {
        $class = str_replace(
            "\\",
            "/",
            preg_replace("/Clickatell\\\/", "", $class)
        );

        if (is_file(__DIR__ . "/" . $class . ".php")) {

            return (boolean) include_once __DIR__ . "/" . $class . ".php";
        } else {

            return false;
        }
    }

    /**
     * Loop through an array of implemented interface and check
     * if the method is defined as an available API.
     *
     * @param array  $interfaces Interfaces to loop through
     * @param string $method     Method name to check for
     *
     * @return boolean
     */
    private function _methodExists($interfaces, $method)
    {
        foreach ($interfaces as $interface) {

            if (method_exists($interface, $method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the transport associated with the request.
     *
     * @param Clickatell\Api\Api $transport Transport to use
     *
     * @return boolean
     */
    public function setTransport(Api $transport)
    {
        $this->_transport = $transport;
    }

    /**
     * Return the transport associated with the request.
     *
     * @return Clickatell\Api\Api
     */
    public function getTransport()
    {
        return $this->_transport;
    }

    /**
     * Utility function to register a new event listener.
     *
     * @param string   $event   Event to listen for
     * @param \Closure $closure Callback to trigger
     *
     * @return boolean
     */
    public function on($event, Closure $closure)
    {
        return Event::on($event, $closure);
    }

    /**
     * Magic to forward an API request to the Transport interface.
     * The Transport interface then ensures the method exists and
     * connects to Clickatell to complete the call. The messenger
     * uses an Action object (the handler) to group these tasks.
     *
     * @param string $name      Method name
     * @param array  $arguments Method arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Get the interface the class uses
        $interfaces = class_implements($this->_transport);

        if ($this->_methodExists($interfaces, $name)) {

            $callArgs = array($name, $arguments);

            return call_user_func_array(array($this->_transport, "call"), $callArgs);

        } else {

            throw new ApiException(ApiException::ERR_METHOD_NOT_FOUND);
        }
    }

    /**
     * Triggers if a clickatell MT callback has been received by the page.
     *
     * @param Closure $closure The callable function
     *
     * @return boolean
     */
    public static function parseCallback(Closure $closure)
    {
        $required = array_flip(
            array(
                'apiMsgId',
                'cliMsgId',
                'to',
                'timestamp',
                'from',
                'status',
                'charge'
            )
        );

        $values = array_intersect_key($_GET, $required);
        $diff = array_diff_key($required, $values);

        // If there are no difference, then it means the callback
        // passed all the required values.
        if (empty($diff))
        {
            return call_user_func_array($closure, array($values));
        }

        return false;
    }
}