<?php
namespace Clickatell\Test\Cases;

#-> Add's an autoloader to load test dependencies
require_once __DIR__ . "/../autoload.php";

use Clickatell\ClickatellContainer as ClickatellContainer;
use Clickatell\Clickatell as Clickatell;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the Container class that serves as
 * an object factory and dependency injector.
 *
 * @package Clickatell\Test\Cases
 * @author Chris Brand
 */
class ClickatellContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the container creates a request object of the 
     * instance Clickatell\Component\Request
     *
     * @return boolean
     */
    public function testCreateRequest()
    {
        $transport = $this->getMock("Clickatell\Component\Transport\TransportInterface");

        $request = ClickatellContainer::createRequest("username", "password", 12345, $transport);

        $this->assertInstanceOf("Clickatell\Component\Request", $request);
    }

    /**
     * Creates and returns an action handler of instance Clickatell\Component\Action
     *
     * @return boolean
     */
    public function testCreateAction()
    {
        $transport = $this->getMock("Clickatell\Component\Transport\TransportInterface");
        $translate = $this->getMock("Clickatell\Component\Translate\TranslateInterface");

        $action = ClickatellContainer::createAction($transport, $translate);

        $this->assertInstanceOf("Clickatell\Component\Action", $action);
    }

    /**
     * Creates a transport object and ensures that is of instance
     * Clickatell\Component\Transport\TransportInterface
     *
     * @return boolean
     */
    public function testCreateTransport()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
                        ->disableOriginalConstructor()
                        ->getMock();

        $action = ClickatellContainer::createTransport(Clickatell::TRANSPORT_HTTP, $request);

        $this->assertInstanceOf("Clickatell\Component\Transport\TransportInterface", $action);
    }

    /**
     * Creates a transfer object of instance Clickatell\Component\Transfer\TransferInterface
     *
     * @return boolean
     */
    public function testCreateTransfer()
    {
        $transfer = ClickatellContainer::createTransfer();

        $this->assertInstanceOf("Clickatell\Component\Transfer\TransferInterface", $transfer);
    }
}