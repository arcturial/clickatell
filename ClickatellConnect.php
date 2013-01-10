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

use Clickatell\Component\Transport\TransportConnect as TransportConnect;
use Clickatell\Component\Transfer\TransferCurl as TransferCurl;
use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;
use Clickatell\Component\Translate\TranslateArray as TranslateArray;
use Clickatell\ClickatellContainer as ClickatellContainer;

/**
 * This is the main messenger class that encapsulates various objects to succesfully
 * send Clickatell calls and respond in an appropriate manner. The messenger class
 * enables you to set your own Transport and Translate interfaces.
 *
 * @category Clickatell
 * @package  ClickatellConnect
 * @author   Thomas Shone <xsist10@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ClickatellConnect
{
    /**
     * The Request Object associated with a Clickatell call.
     * @var Clickatell\Component\Request
     */
    private $_request;

    /**
     * The Transport Object associated with a Clickatell call.
     * @var Clickatell\Component\Transport
     */
    private $_transport;

    /**
     * The Translate Object associated with a Clickatell call.
     * @var Clickatell\Component\Translate
     */
    private $_translate;

    /**
     * The API token allocated by Clickatell
     * @var string
     */
    private $_token;

    /**
     * Clickatell Messenger Instantiation. Creates the Transport/Translate/Request
     * interfaces required.
     *
     * @param string                         $token     Token
     *
     * @return boolean
     */
    public function __construct($token)
    {
        $this->_token = $token;

        // Register autoloader
        $autoload = function ($class) {

            $class = str_replace("\\", "/", preg_replace("/Clickatell\\\/", "", $class));
            
            if (is_file(__DIR__ . "/" . $class . ".php")) {
                include_once __DIR__ . "/" . $class . ".php";   
            }

        };

        spl_autoload_register($autoload);

        // Dependencies
        $this->_request = ClickatellContainer::createRequest();

        $this->_transport = new TransportConnect(
            new TransferCurl(), 
            $this->_request,
            $this->_token
        );

        $this->_translate = new TranslateArray(); // ClickatellContainer::createTranslate();
    }

    /**
     * Sets the Transport interface the Messenger should use.
     *
     * @param Clickatell\Component\Transport\TransportInterface $transport Transport protocol to use
     *
     * @return boolean
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->_transport = $transport;
    }

    /**
     * Returns the Transport interface currently in use.
     *
     * @return Clickatell\Component\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->_transport;
    }

    /**
     * Sets the Translate interface the Messenger should use.
     *
     * @param Cliclatell\Component\Translate\TranslateInterface $translate Translate interface to use
     *
     * @return boolean
     */
    public function setTranslate(TranslateInterface $translate)
    {
        $this->_translate = $translate;
    }

    /**
     * Returns the Translate interface currently in use.
     * 
     * @return Clickatell\Component\Translate\TranslateInterface
     */
    public function getTranslate()
    {
        return $this->_translate;
    }

    /**
     * Returns the Request object associated with the Messenger.
     *
     * @return Clickatell\Component\Request
     */
    public function request()
    {
        return $this->_request;
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
        $action = ClickatellContainer::createAction(
            $this->_transport, 
            $this->_translate
        );

        return call_user_func_array(array($action, $name), $arguments);
    }
}