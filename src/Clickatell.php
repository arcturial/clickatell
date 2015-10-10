<?php
/**
 * The Clickatell SMS Library provides a standardised way of talking to and
 * receiving replies from the Clickatell API's.
 *
 * PHP Version 5.3
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */

namespace Clickatell;


/**
 * This is the main messenger class that encapsulates various objects to succesfully
 * send Clickatell calls and respond in an appropriate manner. The messenger class
 * enables you to set your own Transport and Translate interfaces.
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
abstract class Clickatell implements TransportInterface
{
    const HTTP_GET      = "GET";
    const HTTP_POST     = "POST";
    const HTTP_DELETE   = "DELETE";

    private $secure = false;
    private $agent = "ClickatellPHP/2.1";

    /**
     * Use https or http
     *
     * @param boolean $secure Use https or http
     *
     * @return Clickatell
     */
    public function secure($secure)
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * This function serves as the "request" or "invoke" function. This will in turn
     * call the API or whatever resource is required to complete the task. Each adapter
     * should overwrite this function with the appropriate logic.
     *
     * @param string $uri    The uri (endpoint)
     * @param array  $args   The arguments
     * @param string $method The desired HTTP method
     *
     * @return array
     */
    abstract protected function get($uri, $args, $method = self::HTTP_GET);

    /**
     * Merge the defaults with the requested parameters. The sendMessage API
     * call has many parameters. This call populates some of the defaults for us.
     *
     * @param array $parameters The parameters to merge in
     *
     * @return array
     */
    protected function getSendDefaults($parameters)
    {
        return array_merge(
            array(
                'mo'        => 1,
                'callback'  => 7
            ),
            $parameters
        );
    }

    /**
     * Abstract CURL usage. This helps with testing and extendibility
     * accross multiple API types.
     *
     * @param string $uri     The endpoint
     * @param string $data    POST data or query string
     * @param array  $headers Header array
     * @param string $method  HTTP method
     *
     * @return Decoder
     */
    protected function curl($uri, $data, $headers = array(), $method = self::HTTP_GET)
    {
        // This is the clickatell endpoint. It doesn't really change so
        // it's safe for us to "hardcode" it here.
        $host = "api.clickatell.com";

        $uri = ltrim($uri, "/");
        $uri = ($this->secure ? 'https' : 'http') . '://' . $host . "/" . $uri;
        if ($method === "GET" && !empty($data)) {
            $uri = $uri."?".$data;
        }

        $curlInfo = curl_version();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->agent . ' curl/' . $curlInfo['version'] . ' PHP/' . phpversion());
        ($method == "POST") && curl_setopt($ch, CURLOPT_POST, 1) && curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return new Decoder($result, $httpCode);
    }

    /**
     * Set the user agent for the CURL adapter.
     *
     * @param string $agent The agent string
     *
     * @return Clickatell
     */
    public function setUserAgent($agent)
    {
        $this->agent = $agent;
        return $this;
    }
}
