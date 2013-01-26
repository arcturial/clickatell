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
 * @package  Clickatell\Test\Cases\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Component;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

<<<<<<< HEAD:Test/Cases/Component/TransportTest.php
use Clickatell\Component\Action as Action;
use Clickatell\Component\Transport as Transport;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the transport abstract. 
=======
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
use Clickatell\Component\Utility as Utility;

/**
 * Test Suite for testing the Utility class.
>>>>>>> dev:Test/Cases/Component/UtilityTest.php
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
<<<<<<< HEAD:Test/Cases/Component/TransportTest.php
class TransportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the transport wrapper wraps the response as
     * expected.
     *
     * @return boolean
     */
    public function testWrapResponse()
    {
        $status = Transport::RESULT_SUCCESS;
        $response = "My Response String";

        $expectedResult = array(
            "result" => array(
                "status" => $status, 
                "response" => $response
            )
        );

        $transport = $this->getMockBuilder('Clickatell\Component\Transport')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $transport->expects($this->any())
            ->method('wrapResponse')
            ->will($this->returnValue($expectedResult));

        $this->assertSame(
            $expectedResult, 
            $transport->wrapResponse($status, $response)
        );
=======
class UtilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that array correctly maps to an xml packet string
     *
     * @return boolean
     */
    public function testArrayToString()
    {
        $expectedResult = "<request><param1>12345</param1>"
                          . "<param2>String</param2></request>";

        $arr = array(
            'request' => array(
                'param1' => 12345,
                'param2' => 'String'
            )
        );


        $result = Utility::arrayToString($arr);

        $this->assertSame($expectedResult, $result);
>>>>>>> dev:Test/Cases/Component/UtilityTest.php
    }
}