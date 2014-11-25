<?php
namespace Clickatell;

use Clickatell\Diagnostic;
use \PHPUnit_Framework_TestCase;

class DiagnosticTest extends PHPUnit_Framework_TestCase
{
    public function testGetError()
    {
        $this->assertTrue(strlen(Diagnostic::getError("001")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("002")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("003")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("004")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("005")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("006")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("007")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("008")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("009")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("010")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("011")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("012")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("013")) > 0);
        $this->assertTrue(strlen(Diagnostic::getError("014")) > 0);
        $this->assertSame("unknown error", Diagnostic::getError("015"));
    }
}