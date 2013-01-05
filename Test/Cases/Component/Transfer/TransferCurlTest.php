<?php
namespace Clickatell\Test\Cases\Component\Transfer;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../autoload.php";

use Clickatell\Component\Transfer\TransferCurl as TransferCurl;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TransferCurl class.
 *
 * @package Clickatell\Test\Cases\Component\Transfer
 * @author Chris Brand
 */
class TransferCurlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the basic Curl transfer object and ensure it responds in
     * a suitable way. This method tests its response against a 'real'
     * url and a 'fake' url.
     *
     * @return boolean
     */
    public function testTransfer()
    {
    	$realUrl = "http://www.google.co.za";
    	$fakeUrl = "http://www.google.co.za/not/found.url";

        $transfer = new TransferCurl();
        $transfer->isPost(false);

        #-> Test a real URL transfer
        $result = $transfer->execute($realUrl, "");

        $info = $transfer->info();

        $this->assertSame($realUrl, $info['url']);
        $this->assertSame(200, $info['http_code']);
        $this->assertTrue(!empty($result));

        #-> Test a fake URL transfer
        $result = $transfer->execute($fakeUrl, "");

        $info = $transfer->info();

        $this->assertSame($fakeUrl, $info['url']);
        $this->assertSame(404, $info['http_code']);
        $this->assertTrue(!empty($result));
    }
}