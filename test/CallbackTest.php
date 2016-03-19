<?php
namespace Clickatell;

use Clickatell\Callback;
use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

class CallbackTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Callback::$restrictIP = false;
        Callback::$allowedIPs = array("196.216.236.2");
    }

    public function testIpLockdownSuccess()
    {
        $_SERVER['REMOTE_ADDR'] = "1.1.1.1";
        Callback::$restrictIP = true;
        Callback::$allowedIPs = array("1.1.1.1");

        Callback::parseCallback(function () {});
        Callback::parseMoCallback(function () {});

        // Nothing to assert...as long as no exceptions were thrown..the test passes.
    }

    public function testMtCallbackFail()
    {
        $triggered = false;

        // Should not trigger without all required fields
        $_GET = array(
            'cliMsgId' => 2,
            'to' => 3,
            'timestamp' => 4,
            'from' => 5,
            'status' => 6,
            'charge' => 7
        );

        Callback::parseCallback(function () use (&$triggered) {
            $triggered = true;
        });

        $this->assertFalse($triggered);

        Callback::$restrictIP = true;

        $this->setExpectedException('Exception');
        Callback::parseCallback(function () {});
    }

    public function testMtCallback()
    {
        $data = array();

        $_GET = array(
            'apiMsgId' => 1,
            'cliMsgId' => 2,
            'to' => 3,
            'timestamp' => 4,
            'from' => 5,
            'status' => 6,
            'charge' => 7
        );

        Callback::parseCallback(function ($args) use (&$data) {
            $data = $args;
        });

        $this->assertSame($_GET, $data);
    }

    public function testMoCallbackFail()
    {
        $triggered = false;

        // Should not trigger without all required fields
        $_GET = array(
            'api_id' => 1,
            'from' => 3,
            'to' => 4,
            'timestamp' => 5,
            'network' => 6,
            'text' => 7
        );

        Callback::parseMoCallback(function () use (&$triggered) {
            $triggered = true;
        });

        $this->assertFalse($triggered);

        Callback::$restrictIP = true;

        $this->setExpectedException('Exception');
        Callback::parseMoCallback(function () {});
    }

    public function testMoCallback()
    {
        $data = array();

        $_GET = array(
            'api_id' => 1,
            'moMsgId' => 2,
            'from' => 3,
            'to' => 4,
            'timestamp' => 5,
            'text' => 7,
            'network' => 6
        );

        Callback::parseMoCallback(function ($args) use (&$data) {
            $data = $args;
        });

        $this->assertSame($_GET, $data);

        $_GET['charset'] = 8;
        $_GET['udh'] = 9;

        Callback::parseMoCallback(function ($args) use (&$data) {
            $data = $args;
        });

        $this->assertSame($_GET, $data);
    }
}