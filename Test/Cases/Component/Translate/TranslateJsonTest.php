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
 * @package  Clickatell\Test\Cases\Component\Translate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Component\Translate;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../autoload.php";

use Clickatell\Component\Translate\TranslateJson as TranslateJson;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TranslateJson class.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class TranslateJsonTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the JSON translater correctly morphs an
     * array into a JSON response string.
     *
     * @return boolean
     */
    public function testTranslate()
    {
        $packet = array("param1" => "value1", "param2" => "value2");

        $translate = new TranslateJson();
        $result = $translate->translate($packet);

        $this->assertTrue(!empty($result));

        $result = json_decode($result);

        $this->assertSame("value1", $result->param1);
        $this->assertSame("value2", $result->param2);
    }
}