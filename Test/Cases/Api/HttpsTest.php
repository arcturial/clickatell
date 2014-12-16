<?php


namespace Clickatell\Test\Cases\Api;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../autoload.php";

use Clickatell\Api\Https;

class HttpsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideUrls
     *
     * @param $path
     * @param $expected
     */
    public function testGetUrl($path, $expected)
    {
        $http = new HttpsDummy();
        $this->assertEquals($expected, $http->getUrl($path));
    }

    public function provideUrls()
    {
        return array(
            array('http/sendmsg', 'https://api.clickatell.com/http/sendmsg'),
            array('/http/sendmsg', 'https://api.clickatell.com/http/sendmsg'),
            array('http/getbalance', 'https://api.clickatell.com/http/getbalance'),
            array('http/getmsgcharge', 'https://api.clickatell.com/http/getmsgcharge'),
        );
    }
}

/**
 * Class HttpsDummy used to make getUrl method public
 *
 * @package Clickatell\Test\Cases\Api
 */
class HttpsDummy extends Https
{
    function __construct()
    {
    }

    public function getUrl($path)
    {
        return parent::getUrl($path);
    }
}
