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
 * @package  Clickatell\Component\Transport\Packet
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Transport\Packet;


/**
 * This class is used to convert a stdClass object into an XML string for
 * communication with Clickatell's Connect API
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transport\Packet
 * @author   Thomas Shone <xsist10@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/xsist10
 */
class PacketXml
{
    private $xml;

    function __construct($root_element)
    {
        $this->xml = new \SimpleXMLElement("<$root_element></$root_element>");
    }

    private function addChild($xml, $object)
    {
        foreach ($object as $name => $value)
        {
            if (is_string($value) || is_numeric($value))
            {
                $xml->$name = $value;
            }
            else
            {
                $xml->$name = null;
                $this->iteratechildren($xml->$name, $value);
            }
        }
    }

    function toXml($object)
    {
        $this->addChild($this->xml, $object);
        return $this->xml->asXML();
    }
}