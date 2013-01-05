<?php
namespace Clickatell\Component\Transfer;

use Clickatell\Component\Transfer\TransferInterface as TransferInterface;
use Clickatell\Exception\TransferException as TransferException;

/**
 * This is the Curl Transfer handler. This can be replaced by your framework
 * HTTP handler or you can create a new Transfer handler of your choice.
 * A Transfer handler expects a URL and Parameters and will return the result
 * as a string.
 *
 * @package Clickatell\Component\Transfer
 * @author Chris Brand
 */
class TransferCurl implements TransferInterface
{
	/**
	 * Curl handler
	 * @var Object $_ch
	 */
	private $_ch;

	/**
	 * Creates a new instance of the Transfer curl handler. Sets the default
	 * as a POST request.
	 *
	 * @return boolean
	 */
	public function __construct()
	{
		$this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_HEADER, 0);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->_ch, CURLOPT_POST, 1); 
	}

	/**
	 * Marks a request as POST or GET.
	 *
	 * @param boolean $post
	 * @return boolean
	 */
	public function isPost($post)
	{
        curl_setopt($this->_ch, CURLOPT_POST, $post); 
        return true;
	}

	/**
	 * Executes a transfer request. 
	 *
	 * @param string $url
	 * @param array $param
	 * @return string
	 * @throws TransferException
	 */
	public function execute($url, $param)
	{
		curl_setopt($this->_ch, CURLOPT_URL, $url);

		if (!empty($param))
		{
        	curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $param); 
    	}

        $result = curl_exec($this->_ch);

        if(!curl_errno($this->_ch))
        {
            return $result;
        } 
        else 
        {
            throw new TransferException(TransferException::ERR_HANLDER_EXCEPTION . ": " . curl_error($this->_ch));
        }
	}

	/**
	 * Gets request info from the last request
	 *
	 * @return array
	 */
	public function info()
	{
		return curl_getinfo($this->_ch);
	}

	/**
	 * Closes the curl handler upon script destruction.
	 */
	public function __destruct()
	{
		curl_close($this->_ch);
	}
}