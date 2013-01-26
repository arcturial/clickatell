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

use Clickatell\Exception\ValidateException as ValidateException;
use Clickatell\Component\Validate as Validate;
use \ReflectionClass as ReflectionClass;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the validation class.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the input is numeric.
     *
     * @return boolean
     *
     * @expectedException Clickatell\Exception\ValidateException
     */
    public function testValidateInt()
    {
        $reflection = new ReflectionClass('Clickatell\Component\Validate');
        $method = $reflection->getMethod('_validateInt');
        $method->setAccessible(true);

        $this->assertEquals(true, $method->invokeArgs(null, array("0215556666")));

        // Trigger exception
        $method->invokeArgs(null, array("021a5556666"));
    }

    /**
     * Ensures that the input is a valid telephone number
     *
     * @return boolean
     *
     * @expectedException Clickatell\Exception\ValidateException
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testValidateTelephone()
    {
        $reflection = new ReflectionClass('Clickatell\Component\Validate');
        $method = $reflection->getMethod('_validateTelephone');
        $method->setAccessible(true);

        $this->assertEquals(true, $method->invokeArgs(null, array("27731235678")));

        // Trigger exception
        $method->invokeArgs(null, array("2721a5556666"));

        // Trigger leading 0 error
        $method->invokeArgs(null, array("0215556666"));
    }

    /**
     * Ensures that the input has a value.
     *
     * @return boolean
     *
     * @expectedException Clickatell\Exception\ValidateException
     */
    public function testRequired()
    {
        $reflection = new ReflectionClass('Clickatell\Component\Validate');
        $method = $reflection->getMethod('_validateRequired');
        $method->setAccessible(true);

        $this->assertEquals(true, $method->invokeArgs(null, array("12344")));

        // Trigger exception
        $method->invokeArgs(null, array(''));
    }

    /**
     * Ensures that the validation can traverse nested values
     *
     * @return boolean
     *
     * @expectedException Clickatell\Exception\ValidateException
     */
    public function testTraverse()
    {
        $reflection = new ReflectionClass('Clickatell\Component\Validate');
        $method = $reflection->getMethod('_traverse');
        $method->setAccessible(true);

        // Traverse single
        $this->assertTrue($method->invokeArgs(null, array('12345', '_validateInt')));

        // Trigger exception at nested element
        $this->assertTrue(
            $method->invokeArgs(
                null, 
                array(
                    array('12345', '1234', '12345a', '1234'),
                    '_validateInt'
                )
            )
        );
    }

    /**
     * Ensures that the process of running validation
     * actually triggers a certain field.
     *
     * @return boolean
     *
     * @expectedException Clickatell\Exception\ValidateException
     */
    public function testProcessValidation()
    {
        $method = "sendMessage";
        $args = array("to" => '');

        // We expect a required field error

        Validate::processValidation($method, $args);
    }
}