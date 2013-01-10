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
 * @package  Clickatell\Test\Cases\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Component\Transport\Packet;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../../autoload.php";

use Clickatell\Component\Transport\Packet\PacketXml as PacketXml;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TransportHttp class. This
 * ensures that all the HTTP API requests response as expected
 * and that the utility functions handle it's input/output correctly.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component\Transport
 * @author   Thomas Shone <xsist10@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/xsist10
 */
class PacketXmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that the toXml function works
     *
     * @return boolean
     */
    public function testToXml()
    {
        $params = array('test' => 'message');
        // Build the XML request packet
        $packet = new PacketXml("clickatellsdk");
        $xml = $packet->toXml($params);

        $expected = "<?xml version=\"1.0\"?>\n<clickatellsdk><test>message</test></clickatellsdk>\n";
        $this->assertSame($expected, $xml);
    }
}