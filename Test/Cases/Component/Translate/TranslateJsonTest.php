<?php
namespace Clickatell\Test\Cases\Component\Translate;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../autoload.php";

use Clickatell\Component\Translate\TranslateJson as TranslateJson;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TranslateJson class.
 *
 * @package Clickatell\Test\Cases\Component\Translate
 * @author Chris Brand
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