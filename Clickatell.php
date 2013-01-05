<?php
namespace Clickatell;

use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;
use Clickatell\ClickatellContainer as ClickatellContainer;

/**
 * This is the main messenger class that encapsulates various objects to succesfully
 * send Clickatell calls and respond in an appropriate manner. The messenger class
 * enables you to set your own Transport and Translate interfaces.
 *
 * @package Clickatell
 * @author Chris Brand
 */
class Clickatell
{
    /**
     * The HTTP Transport Interface
     * @var string
     */
    const TRANSPORT_HTTP    = "Clickatell\Component\Transport\TransportHttp";

    /**
     * The XML Transport Interface
     * @var string
     */
    const TRANSPORT_XML     = "Clickatell\Component\Transport\TransportXml";

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
     * Clickatell Messenger Instantiation. Creates the Transport/Translate/Request
     * interfaces required.
     *
     * @param string $username
     * @param string $password
     * @param int $apiId
     * @param $transport Clickatell\Component\Transport
     * @return boolean
     */
    public function __construct($username, $password, $apiId, $transport = null)
    {
        spl_autoload_register(function($class) {
            
            if (is_file(__DIR__ . "/../" . $class . ".php"))
            {
                require_once __DIR__ . "/../" . $class . ".php";   
            }
        });

        if ($transport == null)
        {
            $transport = self::TRANSPORT_HTTP;
        }

        $this->_request = ClickatellContainer::createRequest($username, $password, $apiId);
        $this->_transport = ClickatellContainer::createTransport($transport, $this->_request);
        $this->_translate = ClickatellContainer::createTranslate();
    }

    /**
     * Sets the Transport interface the Messenger should use.
     *
     * @param Clickatell\Component\Transport\TransportInterface $transport
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
     * @param Cliclatell\Component\Translate\TranslateInterface $translate
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
     * Magic to forward an API request to the Transport interface. The Transport
     * interface then ensures the method exists and connects to Clickatell to complete
     * the call. The messenger uses an Action object (the handler) to group these tasks.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $action = ClickatellContainer::createAction($this->_transport, $this->_translate);

        return call_user_func_array(array($action, $name), $arguments);
    }
}