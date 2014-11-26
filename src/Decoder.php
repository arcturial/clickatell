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
 * The response class is a representation of an API response. It provides some
 * helper methods to extract information from a raw response.
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class Decoder
{
    private $body;
    private $status;

    public function __construct($body, $status)
    {
        $this->body = $body;
        $this->status = $status;
    }

    /**
     * Get the response HTTP status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the response body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Decode the REST response.
     *
     * @return stdClass
     */
    public function decodeRest()
    {
        return json_decode($this->body, true);
    }

    /**
     * This method takes a CURL response and tries to unwrap it into
     * a usable object. Since the API is sometimes inconsistent we use
     * this functions to hide all the problems from the developer.
     *
     * @param boolean $multi Do you expect multiple results?
     *
     * @return array
     */
    public function unwrapLegacy($multi = false)
    {
        $lines = explode("\n", trim($this->body, "\n"));
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
            }

            $result[] = $row;
        }

        return $multi ? $result : current($result);
    }
}