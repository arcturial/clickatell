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
 * @package  Clickatell\Component\Transfer
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Transfer;

use Clickatell\Component\Transfer\TransferInterface as TransferInterface;
use Clickatell\Exception\TransferException as TransferException;

/**
 * This is the Curl Transfer handler. This can be replaced by your framework
 * HTTP handler or you can create a new Transfer handler of your choice.
 * A Transfer handler expects a URL and Parameters and will return the result
 * as a string.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transfer
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
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
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_POST, 1); 
    }

    /**
     * Marks a request as POST or GET.
     *
     * @param boolean $post Should it be a POST request or GET
     *
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
     * @param string $url   URL to execute
     * @param array  $param Parameters to pass to URL
     *
     * @return string
     * @throws TransferException
     */
    public function execute($url, $param)
    {
        //echo $url . "?" . $param . "\n";
        curl_setopt($this->_ch, CURLOPT_URL, $url);

        if (!empty($param)) {
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $param); 
        }

        $result = curl_exec($this->_ch);
        //echo $result . "\n";

        if (!curl_errno($this->_ch)) {
            return $result;
        } else {

            throw new TransferException(
                TransferException::ERR_HANLDER_EXCEPTION . ": " 
                . curl_error($this->_ch)
            );
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