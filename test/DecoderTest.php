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

        $this->setExpectedException('Exception', 'Some Error', 301);
        $return = $clickatell->unwrapLegacy();
    }
}