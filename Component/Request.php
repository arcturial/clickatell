<?php
namespace Clickatell\Component;

use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Component\Validate as Validate;

/**
 * This is the Request object. It is a skeleton class that serves
 * as a container for parameters.
 *
 * @package Clickatell\Component
 * @author Chris Brand
 */
class Request
{
    /**
     * Array of request parameters.
     * @var array
     */
    private $_params = array();

    /**
     * Requests objects are invoked with the username/password/apiId
     * of the Request. These params are always in use.
     *
     * @param string $username
     * @param string $password
     * @param int $apiId
     * @return boolean
     */
    public function __construct($username, $password, $apiId)
    {
        $this->_params['user']      = $username;
        $this->_params['password']  = $password;
        $this->_params['api_id']    = $apiId;
    }

    /**
     * Some magic to set parameters into the Request object.
     *
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function __set($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * This resets the request objects, but keeps the username/password/apiId
     * persistent for reuse in the next request.
     *
     * @return Clickatell\Component\Request
     */
    public function reset()
    {
        $tmp = array(
            'user'      => $this->_params['user'],
            'password'  => $this->_params['password'],
            'api_id'    => $this->_params['api_id']
        );

        $this->_params = array();

        $this->_params = array_merge($tmp, $this->_params);

        return $this;
    }

    /**
     * Returns the list of parameters saved in the Request object.
     *
     * @return array
     */
    public function getParams()
    {
        #-> Validate parameters and make sure we are still good to go
        Validate::validateParameters($this->_params);

        return $this->_params;
    }
}