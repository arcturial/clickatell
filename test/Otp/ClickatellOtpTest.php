<?php
namespace Clickatell;

use Clickatell\Otp\ClickatellOtp;
use Clickatell\Otp\SessionStorage;
use \PHPUnit_Framework_TestCase;
use \ReflectionClass;
use \stdClass;

class ClickatellOtpTest extends PHPUnit_Framework_TestCase
{
    private $storage, $message;

    public function setUp()
    {
        $this->message = $this->getMockForAbstractClass('Clickatell\Clickatell');
        $this->storage = $this->getMock('Clickatell\Otp\StorageInterface');
    }

    public function testGenerateToken()
    {
        $clickatell = new ClickatellOtp($this->message, $this->storage);
        $class = new ReflectionClass($clickatell);
        $method = $class->getMethod('generateToken');
        $method->setAccessible(true);
        $return = $method->invoke($clickatell);

        $this->assertSame(7, strlen($return));
    }

    public function testSendPin()
    {
        $generated = "token message";
        $return = new stdClass;
        $return->id = "A1234";
        $to = 12345;

        $clickatell = $this->getMockBuilder('Clickatell\Otp\ClickatellOtp')
            ->setConstructorArgs(array($this->message, $this->storage))
            ->setMethods(array('generateMessage'))
            ->getMock();

        $this->message->expects($this->once())
            ->method('sendMessage')
            ->with($to, $generated)
            ->will($this->returnValue(array($return)));

        $clickatell->expects($this->once())
            ->method('generateMessage')
            ->will($this->returnValue($generated));

        $id = $clickatell->sendPin($to);
        $this->assertSame($return->id, $id);
    }

    public function testSendPinFail()
    {
        $return = new stdClass;
        $return->id = false;
        $return->error = "Some error";
        $return->errorCode = 1;
        $to = 12345;

        $clickatell = new ClickatellOtp($this->message, $this->storage);

        $this->message->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue(array($return)));

        $this->setExpectedException('Exception', $return->error, $return->errorCode);
        $clickatell->sendPin($to);
    }

    public function testVerifyPin()
    {
        $to = 12345;
        $token = "token";

        $this->storage->expects($this->once())
            ->method('get')
            ->with(md5($to))
            ->will($this->returnValue($token));

        $clickatell = new ClickatellOtp($this->message, $this->storage);
        $this->assertTrue($clickatell->verifyPin($to, $token));
    }

    public function testFunctional()
    {
        $return = new stdClass;
        $return->id = "1234";

        $this->message->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue(array($return)));

        $to = 1234;
        $storage = new SessionStorage;
        $clickatell = new ClickatellOtp($this->message, $storage);

        $id = $clickatell->sendPin($to);
        $this->assertSame($return->id, $id);

        $token = $storage->get(md5($to));
        $this->assertTrue($clickatell->verifyPin($to, $token));
    }
}