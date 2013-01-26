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
 * @package  Clickatell\Component\Translate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Translate;

use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;

/**
 * This is the XML Translater. It takes a response from the API
 * and turns it into a nicely formatted XML packet.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Translate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class TranslateXml implements TranslateInterface
{
    /**
     * Translates an array to XML
     *
     * @param array $response Array to translate
     *
     * @return string
     */
    public function translate(array $response)
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

        $result = array_map($map, array($response));
        
        return array_shift($result);
    }
}