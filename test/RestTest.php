<?php
namespace Clickatell;

use \PHPUnit_Framework_TestCase;

class RestTest extends PHPUnit_Framework_TestCase
{
    public function testStage()
    {
        $rest = new \Clickatell\Rest('token');

        $rest->sendMessage(['to' => 27724967515]);
    }
}