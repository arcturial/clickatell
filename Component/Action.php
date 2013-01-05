<?php
namespace Clickatell\Component;

use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;
use Clickatell\Exception\TransportException as TransportException;
use \ReflectionMethod as ReflectionMethod;

/**
 * This is the generic Action handler. The Action handler groups
 * the request to the Transport and Translater together and returns
 * a nicely translated response to the Messenger object.
 *
 * @package Clickatell\Component
 * @author Chris Brand
 */
class Action
{
    /**
     * Transport object associated with the Request.
     * @var Clickatell\Component\Transport\TransportInterface
     */
    private $_transport;

    /**
     * Translate object associated with the Request.
     * @var Clickatell\Component\Translate\TranslateInterface
     */
    private $_translate;

    /**
     * Action handler requires you to give it the desired Transport Interface and
     * the desired Translater. These objects can be manipulated through the Messenger class.
     *
     * @see Clickatell\Clickatell.php
     * @param Clickatell\Component\Transport\TransportInterface $transport
     * @param Clickatell\Component\Translate\TranslateInterface $translate
     * @return boolean
     */
    public function __construct(TransportInterface $transport, TranslateInterface $translate)
    {
        $this->_transport = $transport;
        $this->_translate = $translate;
    }

    /**
     * Returns the Transport associated with this Action request.
     *
     * @return Clickatell\Component\Transport\TransportInterface
     */
    public function transport()
    {
        return $this->_transport;
    }

    /**
     * Magic to forward the request from the Messenger on to the Transport associated
     * with this Action. It also ensures that the request is actually supported.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Clickatell\Exception\TransportException
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->_transport, $name))
        {
            return $this->_translate->translate(call_user_func_array(array($this->_transport, $name), $arguments));
        }
        else
        {
            throw new TransportException(TransportException::ERR_METHOD_NOT_FOUND);
        }
    }
}