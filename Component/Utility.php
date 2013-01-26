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
 * @package  Clickatell\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component;

use Clickatell\Component\Transfer\TransferInterface as TransferInterface;
use Clickatell\Exception\TransportException as TransportException;
use Clickatell\Exception\ValidateException as ValidateException;
use Clickatell\Component\Request as Request;

/**
 * Utility class for some shared functionality. 
 *
 * @category Clickatell
 * @package  Clickatell\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @abstract
 */
class Utility
{
    /**
     * Converts an array to a string xml
     * packet.
     *
     * @param array $array Array to convert
     *
     * @return string
     */
    public static function arrayToString(array $array)
    {
        $map = function ($array) use (&$map) {

            $return = "";
            
            foreach ($array as $key => $val) {

                if (is_array($val)) {

                    $data = array_map($map, array($val));
                    $data = implode("", $data);
                    
                    $return .= "<" . $key . ">" . $data . "</". $key .">";

                } else {

                    $return .= "<" . $key . ">" . $val . "</". $key .">";
                }
            }

            return $return;
        };

        $result = array_map($map, array($array));
        
        return array_shift($result);
    } 
}