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

use Clickatell\TransportInterface;
use Clickatell\Response;
use \Exception;

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
    const HTTP_GET  = "GET";
    const HTTP_POST = "POST";

    private $secure = false;

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
     * Abstract CURL usage. This helps with testing and extendibility
     * accross multiple API types.
     *
     *
     * @return array
     */
    protected function curl($uri, $args, $headers = array(), $method = self::HTTP_GET)
    {
        // This is the clickatell endpoint. It doesn't really change so
        // it's safe for us to "hardcode" it here.
        $host = "api.clickatell.com";

        $uri = ltrim($uri, "/");
        $uri = ($this->secure ? 'https' : 'http') . '://' . $host . "/" . $uri;
        $query = http_build_query($args);
        $method == "GET" && $uri = $uri . "?"  . $query;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, ($method == "POST"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return array('body' => $result, 'code' => $httpCode);
    }

    /**
     * This method takes a CURL response and tries to unwrap it into
     * a usable object. Since the API is sometimes inconsistent we use
     * this functions to hide all the problems from the developer.
     *
     * @param string  $body      The content body
     * @param boolean $multi     Do you expect multiple results?
     * @param boolean $exception Do you want to throw an exception on failure?
     *
     * @return array
     */
    protected function unwrapLegacy($body, $multi = false, $exception = false)
    {
        $lines = explode("\n", trim($body, "\n"));
        $result = array();

        foreach ($lines as $line) {
            preg_match_all("/([A-Za-z]+):((.(?![A-Za-z]+:))*)/", $line, $matches);

            $row = array();
            foreach ($matches[1] as $index => $status) {
                $row[$status] = trim($matches[2][$index]);
            }

            if (isset($row['ERR'])) {
                $error = explode(",", $row['ERR']);
                $row['error'] = true;
                $row['code'] = $error[0];
                $row['error'] = trim($error[1]);
                unset($row['ERR']);

                if ($exception) {
                    throw new Exception($row['error'], $row['code']);
                }
            }

            $result[] = $row;
        }

        return $multi ? $result : current($result);
    }

    /**
     * Triggers if a clickatell MT callback has been received by the page.
     *
     * @param Closure $closure The callable function
     *
     * @return boolean
     */
    /*
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
    */
}