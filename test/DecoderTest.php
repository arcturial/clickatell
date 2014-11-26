<?php
namespace Clickatell;

use Clickatell\Decoder;
use \PHPUnit_Framework_TestCase;

class DecoderTest extends PHPUnit_Framework_TestCase
{
    public function testUnwrapLegacy()
    {
        $body = "ERR: 301, Some Error";
        $clickatell = new Decoder($body, 200);

        $return = $clickatell->unwrapLegacy();
        $this->assertSame('Some Error', $return['error']);
        $this->assertEquals(301, $return['code']);
    }
}