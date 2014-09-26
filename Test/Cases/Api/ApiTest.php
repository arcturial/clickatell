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
 * @package  Clickatell\Test\Cases\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Api;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use Clickatell\Api\Api as Api;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
use \ReflectionClass as ReflectionClass;

/**
 * Test Suite for testing the inherited abilities of the
 * default API abstract.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensure the extractExtra function maps the values correctly.
     *
     * @return boolean
     */
    public function testExtractExtra()
    {
        $api = $this->getMockBuilder('Clickatell\Api\Api')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Make the protected method accesible
        $reflection = new ReflectionClass($api);
        $method = $reflection->getMethod('extractExtra');
        $method->setAccessible(true);

        $packet = array();
        $extra = array('delivery_time' => 10, 'max_credits' => 20);
        $method->invokeArgs($api, array($extra, &$packet));

        $this->assertSame(array('deliv_time' => 10, 'max_credits' => 20), $packet);
    }

    /**
     * Ensure that the call curl can reach the outside and
     * do some nice work for us.
     *
     * @return boolean
     */
    public function testCallApi()
    {
        $api = $this->getMockBuilder('Clickatell\Api\Api')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Make the private method accesible
        $reflection = new ReflectionClass($api);
        $method = $reflection->getMethod('callApi');
        $method->setAccessible(true);

        // Load a random clickatell library component
        $result = $method->invokeArgs(
            $api,
            array('http://api.clickatell.com/http/sendmsg', array())
        );

        $this->assertTrue(!empty($result));
    }

    /**
     * Ensure the url encoding is done correctly.
     *
     * @return boolean
     */
    public function testEncoding()
    {
        // Set the query string and build up the packet.
        $packet = array();
        $query = "param1=value&param2=entry+something&param3=entry something";

        foreach (explode("&", $query) as $row)
        {
            $entry = explode("=", $row);

            $packet[$entry[0]] = $entry[1];
        }


        $api = $this->getMockBuilder('Clickatell\Api\Api')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Make the private method accesible
        $reflection = new ReflectionClass($api);
        $method = $reflection->getMethod('_buildQueryString');
        $method->setAccessible(true);

        // Load a random clickatell library component
        $result = $method->invokeArgs($api, array($packet));

        // Parse the response
        parse_str($result, $output);

        $this->assertSame($packet, $output);
    }

    /**
     * Ensures that the transport wrapper wraps the response as
     * expected.
     *
     * @return boolean
     */
    public function testWrapResponse()
    {
        $status = Api::RESULT_SUCCESS;
        $response = "My Response String";

        // Set the result as we expect it
        $expectedResult = array(
            "result" => array(
                "status" => $status,
                "response" => $response
            )
        );

        // Mock the abstract class, since we can't instantiate it
        $transport = $this->getMockBuilder('Clickatell\Api\Api')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Make the private method accesible
        $reflection = new ReflectionClass($transport);
        $method = $reflection->getMethod('wrapResponse');
        $method->setAccessible(true);

        $this->assertSame(
            $expectedResult,
            $method->invokeArgs($transport, array($status, $response))
        );
    }

    /**
     * Test the default api extraction behaviour
     *
     * @return boolean
     */
    public function testExtract()
    {
        $apiResult = "OK: Success Credit: 1";

        // Mock the abstract class, since we can't instantiate it
        $transport = $this->getMockBuilder('Clickatell\Api\Api')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Make the private method accesible
        $reflection = new ReflectionClass($transport);
        $method = $reflection->getMethod('extract');
        $method->setAccessible(true);


        $this->assertSame(
            array(
                "OK" => "Success",
                "Credit" => "1"
            ),
            $method->invokeArgs($transport, array($apiResult))
        );
    }

    /**
     * Make sure the translater changes when we request
     * a change
     *
     * @return boolean
     */
    public function testSetTranslater()
    {
        // Create a translate mock
        $translate = $this->getMock(
            'Clickatell\Component\Translate\TranslateInterface'
        );

        // Mock the abstract class, since we can't instantiate it
        $transport = $this->getMockBuilder('Clickatell\Api\Api')
            ->setConstructorArgs(array($translate))
            ->getMockForAbstractClass();

        // Check that the translater is ok
        $this->assertSame($translate, $transport->getTranslater());

        // Change the translater
        $translateNew = $this->getMock(
            'Clickatell\Component\Translate\TranslateInterface'
        );

        $transport->setTranslater($translateNew);

        // Check that the translater is still ok
        $this->assertSame($translateNew, $transport->getTranslater());
    }

    /**
     * Set the authentication associated with the
     * request
     *
     * @return boolean
     */
    public function testAuthentication()
    {
        $user = 'user';
        $password = 'password';
        $apiId = 12345;

        // Mock the abstract class, since we can't instantiate it
        $transport = $this->getMockBuilder('Clickatell\Api\Api')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Set the auth
        $transport->authenticate($user, $password, $apiId);

        // Check the auth now
        $reflection = new ReflectionClass($transport);
        $property = $reflection->getProperty('auth');
        $property->setAccessible(true);

        $this->assertSame(
            $property->getValue($transport),
            array(
                'user' => $user,
                'password' => $password,
                'api_id' => $apiId
            )
        );
    }

    /**
     * Test the 'call()' mechanism and make sure it triggers
     * the translater like it is suppose to.
     *
     * @return boolean
     */
    public function testMethodInvocation()
    {
        // Setup some calls and results.
        // Call getTranslater just because we don't
        // have access to API functions without
        // introducing more dependencies
        $methodToCall = 'sendMessage';

        $args = array(
            0 => array('123456789'),
            1 => 'Message to send'
        );

        $result = array("result" => "success");
        $translateResult = json_encode($result);

        // Create a translate mock
        $translate = $this->getMock(
            'Clickatell\Component\Translate\TranslateInterface'
        );

        // Mock the abstract class, since we can't instantiate it
        $transport = $this->getMockBuilder('Clickatell\Api\Http')
            ->setConstructorArgs(array($translate))
            ->setMethods(array($methodToCall))
            ->getMock();

        // Make sure the transport gets called with the method
        $transport->expects($this->any())
            ->method($methodToCall)
            ->with($this->equalTo($args[0]), $this->equalTo($args[1]))
            ->will($this->returnValue($result));

        // Make sure the translater gets triggered with what it needs
        $translate->expects($this->any())
            ->method('translate')
            ->with($this->equalTo($result))
            ->will($this->returnValue($translateResult));

        // Call the method that should go through the translater
        $calledResult = $transport->call(
            $methodToCall,
            $args
        );

        // Check the expected result
        $this->assertSame($translateResult, $calledResult);
    }

}