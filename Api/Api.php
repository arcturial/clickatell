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
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Api;

use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;
use Clickatell\Exception\TransferException as TransferException;
use Clickatell\Component\Event as Event;
use \ReflectionClass as ReflectionClass;
use \InvalidArgumentException;

/**
 * This is an abstraction of the Api class. It contains some default
 * setup an utility functions.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @abstract
 */
abstract class Api
{
    /**
     * Failed API Response
     * @var string
     */
    const RESULT_FAILURE = "failure";

    /**
     * Successful API Response
     * @var string
     */
    const RESULT_SUCCESS = "success";

    /**
     * The translater to use when returning the data to the user
     * @var Clickatell\Component\Translate\TranslateInterface
     */
    private $_translate;

    /**
     * Supported 'extra' parameters
     * @var array
     */
    private $_supportedExtra = array(
        'delivery_time' => 'deliv_time',
        'concatenation' => 'concat',
        'max_credits' => 'max_credits',
        'required_features' => 'req_feat',
        'unicode' => 'unicode',
        'mobile_originated' => 'mo',
        'client_message_id' => 'cliMsgId'
    );

    /**
     * Stores the authentication details
     * @var array
     */
    protected $auth;

    /**
     * Instantiation of an API. This interface connects to Clickatell
     * retrieves the data and does a call to translate the data
     * into a known format.
     *
     * @param Clickatell\Component\Translate\TranslateInterface $translate Translate interface to use
     *
     * @return boolean
     */
    public function __construct(TranslateInterface $translate)
    {
        $this->_translate = $translate;
    }

    /**
     * Utility function to map method parameters
     * to the arguments passed. This helps with producing
     * nicely formatted request packets.
     *
     * @param string $method Method to check
     * @param array  $args   Arguments to map
     *
     * @return array
     */
    private function _mapArgs($method, array $args)
    {
        $class = new ReflectionClass($this);
        $method = $class->getMethod($method);

        $params = $method->getParameters();

        $result = array();

        for ($i = 0; $i < count($params); $i++) {
            if (isset($args[$i])) {
                $result[$params[$i]->name] = $args[$i];
            }
        }

        return $result;
    }

    /**
     * Turn an array packet into url encoded query string.
     *
     * @param array $packet The packet to encode
     *
     * @return string
     */
    private function _buildQueryString(array $packet)
    {
        return http_build_query($packet);
    }

    /**
     * Do a CURL request to call the API.
     *
     * @param string  $url     URL to call
     * @param array   $packet  Packet to process
     *
     * @return string
     */
    protected function callApi($url, array $packet)
    {
        // Check if we have CURL
        if (!function_exists('curl_init')) {
            throw new TransferException(TransferException::ERR_CURL_DISABLED);
        }

        $post = $this->_buildQueryString($packet);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);

        // Check if the call completed
        if (!curl_errno($ch)) {

            return $result;

        } else {

            throw new TransferException(TransferException::ERR_HANLDER_EXCEPTION);
        }
    }

    /**
     * Extract array keys in the $extra parameters to the an
     * array.
     *
     * @param array $extra  The extra parameters
     * @param array $packet The packet to send to the API
     *
     * @return boolean
     */
    protected function extractExtra($extra, &$packet)
    {
        foreach ($extra as $key => $value)
        {
            if (array_key_exists($key, $this->_supportedExtra))
            {
                $packet[$this->_supportedExtra[$key]] = $value;
            }
            else
            {
                throw new InvalidArgumentException(
                    '"' . $key . '" parameter not supported. (supported: '
                    . implode(",", $this->_supportedExtra) . ')'
                );
            }
        }
    }

    /**
     * Transports needs a way to extract data from the API call. Most
     * API calls sort of have the same response. So this serves as the generic
     * extracter. It can be overwritten for custom Transports.
     *
     * @param string  $response Response from API
     * @param boolean $multi    Should this result return an array of values
     *
     * @return array
     */
    protected function extract($response, $multi = false)
    {
        $lines = explode("\n", trim($response, "\n"));
        $result = array();

        foreach ($lines as $line) {
            preg_match_all("/([A-Za-z]+):((.(?![A-Za-z]+:))*)/", $line, $matches);

            $row = array();
            foreach ($matches[1] as $index => $status) {
                $row[$status] = trim($matches[2][$index]);
            }

            $result[] = $row;
        }

        return $multi ? $result : current($result);
    }

    /**
     * Utility function to wrap the response from the API in generic
     * array format that can be translate by the Translater.
     *
     * @param strng  $status   API call status
     * @param string $response API response
     *
     * @return array
     */
    protected function wrapResponse($status, $response)
    {
        $result = array(
            "result" => array(
                    "status" => $status
                )
            );

        $result['result']['response'] = $response;

        return $result;
    }

    /**
     * Authenticates the current API request and stores the auth details.
     *
     * @param string $user     Username to auth
     * @param string $password Password to use
     * @param int    $apiId    ApiID to call
     *
     * @return boolean
     */
    public function authenticate($user, $password, $apiId)
    {
        $this->auth['user'] = $user;
        $this->auth['password'] = $password;
        $this->auth['api_id'] = $apiId;

        return true;
    }

    /**
     * Set the data translater to use. The translater will take the
     * result array and format it to the desired input.
     *
     * @param Clicatell\Component\TranslateInterface $translate Translater to use
     *
     * @return boolean
     */
    public function setTranslater(TranslateInterface $translate)
    {
        $this->_translate = $translate;
    }

    /**
     * Get the translater being used by this transport currently.
     *
     * @return Clickatell\Component\Translate\TranslateInterface
     */
    public function getTranslater()
    {
        return $this->_translate;
    }

    /**
     * The call handler that wraps all call responses with a translater.
     *
     * @param string $method Method to call
     * @param array  $args   The packet to process
     *
     * @return mixed
     */
    public function call($method, array $args)
    {
        // Trigger request event
        $mapArgs = $this->_mapArgs($method, $args);

        $eventArgs = array_merge(
            array('call' => $method),
            array('request' => &$mapArgs)
        );

        Event::trigger('request', $eventArgs);

        // Execute the method
        $result = $this->_translate->translate(
            call_user_func_array(array($this, $method), $mapArgs)
        );

        // Trigger response event
        Event::trigger('response', $result);

        return $result;
    }
}
