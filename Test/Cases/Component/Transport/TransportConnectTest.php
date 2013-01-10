<?php
/**
 * The Clickatell SMS Library provides a standardised way of talking to and
 * receiving replies from the Clickatell API's. It makes it
 * easier to write your applications and grants the ability to
 * quickly switch the type of API you want to use HTTP/XML without
 * changing any code.
 *
 * PHP Version 5.3
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Test\Cases\Component\Transport;

// Add's an autoloader to load test dependencies
require_once __DIR__ . "/../../../autoload.php";

use Clickatell\Component\Transport\TransportConnect as TransportConnect;
use Clickatell\Component\Transport as Transport;
use Clickatell\Exception\Diagnostic as Diagnostic;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

/**
 * Test Suite for testing the TransportHttp class. This
 * ensures that all the HTTP API requests response as expected
 * and that the utility functions handle it's input/output correctly.
 *
 * @category Clickatell
 * @package  Clickatell\Test\Cases\Component\Transport
 * @author   Thomas Shone <xsist10@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/xsist10
 */
class TransportConnectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the buildPost function for the Connect class
     * translates the Request parameter array into a query string.
     *
     * @return boolean
     */
    public function testBuildPost()
    {
        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("to" => 12345, "action" => "tmp")));

        $transport = new TransportConnect($transfer, $request, "abc123");
        $result = $transport->buildPost($request);

        $this->assertSame("XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cto%3E12345%3C%2Fto%3E%3CAction%3Etmp%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A", $result);
    }

    /**
     * Ensure that the function responsible for taking the string and 
     * parsing it into an array is still working as expected.
     *
     * @return boolean
     */
    public function testExtract()
    {
        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $transport = new TransportConnect($transfer, $request, "abc123");
        $result = $transport->extract('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_callback</Action><Result>Success</Result><Values><Value><callback_type_id>0</callback_type_id><callback_type>HTTP Get</callback_type></Value><Value><callback_type_id>1</callback_type_id><callback_type>HTTP Post</callback_type></Value><Value><callback_type_id>2</callback_type_id><callback_type>XML Post</callback_type></Value><Value><callback_type_id>3</callback_type_id><callback_type>XML Get</callback_type></Value><Value><callback_type_id>5</callback_type_id><callback_type>SOAP Get</callback_type></Value><Value><callback_type_id>6</callback_type_id><callback_type>SOAP Post</callback_type></Value></Values><Timestamp>1357662097</Timestamp></CLICKATELLSDK>');

        $expected = array(
            'Action' => 'get_list_callback',
            'Result' => 'Success',
            'Values' => array(
                array(
                    'callback_type_id'  => '0',
                    'callback_type'     => 'HTTP Get',
                ),
                array(
                    'callback_type_id'  => '1',
                    'callback_type'     => 'HTTP Post',
                ),
                array(
                    'callback_type_id'  => '2',
                    'callback_type'     => 'XML Post',
                ),

                array(
                    'callback_type_id'  => '3',
                    'callback_type'     => 'XML Get',
                ),

                array(
                    'callback_type_id'  => '5',
                    'callback_type'     => 'SOAP Get',
                ),

                array(
                    'callback_type_id'  => '6',
                    'callback_type'     => 'SOAP Post',
                ),
            ),
            'Timestamp' => '1357662097',
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Ensures that "get_list_country" calling and replying as expected
     *
     * @return boolean.
     */
    public function testGetListCountry()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("action" => "get_list_country")));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3CAction%3Eget_list_country%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_callback</Action><Result>Success</Result><Values><Value><callback_type_id>0</callback_type_id><callback_type>HTTP Get</callback_type></Value><Value><callback_type_id>1</callback_type_id><callback_type>HTTP Post</callback_type></Value><Value><callback_type_id>2</callback_type_id><callback_type>XML Post</callback_type></Value></Values><Timestamp>1357662097</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListCountry();

        $expected = array(
            array(
                'callback_type_id'  => '0',
                'callback_type'     => 'HTTP Get',
            ),
            array(
                'callback_type_id'  => '1',
                'callback_type'     => 'HTTP Post',
            ),
            array(
                'callback_type_id'  => '2',
                'callback_type'     => 'XML Post',
            ),
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Ensures that "get_list_country_prefix" Connect XML call is still working the way it should.
     *
     * @return boolean
     */
    public function testGetListCountryPrefix()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("action" => "get_list_country_prefix")));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3CAction%3Eget_list_country_prefix%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_country_prefix</Action><Result>Success</Result><Values><Value><country_id>1</country_id><prefix>93</prefix></Value><Value><country_id>2</country_id><prefix>355</prefix></Value><Value><country_id>3</country_id><prefix>213</prefix></Value></Values><Timestamp>1357668210</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListCountry();

        $expected = array(
            array(
                'country_id'    => '1',
                'prefix'        => '93'
            ),
            array(
                'country_id'    => '2',
                'prefix'        => '355'
            ),
            array(
                'country_id'    => '3',
                'prefix'        => '213'
            ),
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the list of accounts that can be created
     *
     * @return array
     */
    public function testGetListAccount()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("action" => "get_list_account")));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3CAction%3Eget_list_account%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_account</Action><Result>Success</Result><Values><Value><account_id>1</account_id><account_type>International</account_type></Value><Value><account_id>2</account_id><account_type>South Africa Only Account</account_type></Value><Value><account_id>3</account_id><account_type>UK Only Account</account_type></Value><Value><account_id>4</account_id><account_type>US Only Account (Enterprize)</account_type></Value><Value><account_id>6</account_id><account_type>Ireland Only Account</account_type></Value><Value><account_id>7</account_id><account_type>India Only Account</account_type></Value><Value><account_id>9</account_id><account_type>US Only Account (Small Business)</account_type></Value></Values><Timestamp>1357718597</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListCountry();

        $expected = array(
            array(
                'account_id'    => '1',
                'account_type'   => 'International',
            ),
            array(
                'account_id'    => '2',
                'account_type'  => 'South Africa Only Account',
            ),
            array(
                'account_id'    => '3',
                'account_type'   => 'UK Only Account',
            ),
            array(
                'account_id'    => '4',
                'account_type'   => 'US Only Account (Enterprize)',
            ),
            array(
                'account_id'    => '6',
                'account_type'  => 'Ireland Only Account',
            ),
            array(
                'account_id'    => '7',
                'account_type'  => 'India Only Account',
            ),
            array(
                'account_id'    => '9',
                'account_type'   => 'US Only Account (Small Business)',
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the terms of use based on country/ip_address that can be created
     *
     * @return array
     */
    public function testGetListTerms()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("action" => "get_list_terms", "client_ip_address" => "196.15.145.15")));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cclient_ip_address%3E196.15.145.15%3C%2Fclient_ip_address%3E%3CAction%3Eget_list_terms%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_terms</Action><Result>Success</Result><Values><Value><URL_location>http://www.clickatell.com/sdk/terms.txt</URL_location></Value></Values><Timestamp>1357725839</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListCountry();

        $expected = array(
            'URL_location' => 'http://www.clickatell.com/sdk/terms.txt'
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the register function
     *
     * @return array
     */
    public function testRegister()
    {
        $data = array(
            "user"              => "test.test",
            "fname"             => "test",
            "sname"             => "test",
            "password"          => "password",
            "email_address"     => "test@test.com",
            "mobile_number"     => "27840000000",
            "country_id"        => 252,
            "captcha_code"      => 1,
            "captcha_id"        => 1,
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "register",
                    "accept_terms"      => 1,
                    "force_create"      => 1,
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cfname%3Etest%3C%2Ffname%3E%3Csname%3Etest%3C%2Fsname%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3Cemail_address%3Etest%40test.com%3C%2Femail_address%3E%3Cmobile_number%3E27840000000%3C%2Fmobile_number%3E%3Ccountry_id%3E252%3C%2Fcountry_id%3E%3Ccaptcha_code%3E1%3C%2Fcaptcha_code%3E%3Ccaptcha_id%3E1%3C%2Fcaptcha_id%3E%3Caccept_terms%3E1%3C%2Faccept_terms%3E%3Cforce_create%3E1%3C%2Fforce_create%3E%3CAction%3Eregister%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>register</Action><Result>Success</Result><Values/><Timestamp>1357729431</Timestamp></CLICKATELLSDK>'));
        
        $result = $transport->register($data);

        $expected = array(
            'result' => array(
                'status'    => 'success',
                'response'  => '',
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the resend email activation function
     *
     * @return array
     */
    public function testResendEmailActivation()
    {
        $data = array(
            "user"              => "test.test",
            "password"          => "password",
            "email_address"     => "test@test.com",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "resend_email_activation",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3Cemail_address%3Etest%40test.com%3C%2Femail_address%3E%3CAction%3Eresend_email_activation%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>resend_email_activation</Action><Result>Success</Result><Values/><Timestamp>1357730122</Timestamp></CLICKATELLSDK>'));
        
        $result = $transport->resendEmailActivation($data);

        $expected = array(
            'result' => array(
                'status'    => 'success',
                'response'  => '',
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the authenticate user function
     *
     * @return array
     */
    public function testAuthenticateUser()
    {
        $data = array(
            "user"              => "test.test",
            "password"          => "password",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "authenticate_user",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3CAction%3Eauthenticate_user%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<CLICKATELLSDK><Action>authenticate_user</Action><Result>Success</Result><Values><Value><Usernumber>3401455</Usernumber></Value></Values><Timestamp>1357731930</Timestamp></CLICKATELLSDK>'));
        
        $result = $transport->authenticateUser($data);

        $expected = array(
            'Usernumber' => '3401455'
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test a failed authenticate user call
     *
     * @return array
     */
    public function testAuthenticateUserFailed()
    {
        $data = array(
            "user"              => "test.test",
            "password"          => "password",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "authenticate_user",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3CAction%3Eauthenticate_user%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<CLICKATELLSDK><Result>Error</Result><Error>103</Error><Description>Not email activated</Description><Value><Email>test@test.com</Email></Value><Timestamp>1357731016</Timestamp></CLICKATELLSDK>'));
        
        $result = $transport->authenticateUser($data);

        $expected = array(
            'result' => array(
                'status'    => 'failure',
                'response'  => 'Not email activated'
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test a forgot password call
     *
     * @return array
     */
    public function testForgotPassword()
    {
        $data = array(
            "user"              => "test.test",
            "email_address"     => "test@test.com",
            "captcha_code"      => "1",
            "captcha_id"        => "1",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "forgot_password",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cemail_address%3Etest%40test.com%3C%2Femail_address%3E%3Ccaptcha_code%3E1%3C%2Fcaptcha_code%3E%3Ccaptcha_id%3E1%3C%2Fcaptcha_id%3E%3CAction%3Eforgot_password%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>forgot_password</Action><Result>Success</Result><Values/><Timestamp>1357814866</Timestamp></CLICKATELLSDK>'));
        
        $result = $transport->forgotPassword($data);

        $expected = array(
            'result' => array(
                'status'    => 'success',
                'response'  => ''
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the get captcha code
     *
     * @return array
     */
    public function testGetCaptcha()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("action" => "get_captcha")));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3CAction%3Eget_captcha%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_captcha</Action><Result>Success</Result><Values><Value><captcha_id>c5ab965ebf5377b1ed85088dff243e1b</captcha_id><captcha_image>PNG</captcha_image></Value></Values><Timestamp>1357814302</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListCountry();

        $expected = array(
            'captcha_id'    => 'c5ab965ebf5377b1ed85088dff243e1b',
            'captcha_image' => 'PNG'
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the SMS Activation Status code
     *
     * @return array
     */
    public function testSmsActivationStatus()
    {
        $data = array(
            "user"              => "test.test",
            "password"          => "password",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "sms_activation_status",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3CAction%3Esms_activation_status%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Result>Error</Result><Error>104</Error><Description>Not SMS activated</Description><Value><Cellphone>27999900001</Cellphone></Value><Timestamp>1357817945</Timestamp></CLICKATELLSDK>'));

        $result = $transport->smsActivationStatus($data);

        $expected = array(
            'result' => array(
                'status'    => 'failure',
                'response'  => 'Not SMS activated'
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the SMS Activation Status code
     *
     * @return array
     */
    public function testSendActivationSms()
    {
        $data = array(
            "user"              => "test.test",
            "password"          => "password",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "send_activation_sms",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3CAction%3Esend_activation_sms%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>send_activation_sms</Action><Result>Success</Result><Values/><Timestamp>1357818581</Timestamp></CLICKATELLSDK>'));

        $result = $transport->sendActivationSms($data);

        $expected = array(
            'result' => array(
                'status'    => 'success',
                'response'  => ''
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the SMS Activation validation code
     *
     * @return array
     */
    public function testValidateActivationSms()
    {
        $data = array(
            "user"                  => "test.test",
            "password"              => "password",
            "sms_activation_code"   => "abc123"
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "validate_activation_sms",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3Csms_activation_code%3Eabc123%3C%2Fsms_activation_code%3E%3CAction%3Evalidate_activation_sms%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>send_activation_sms</Action><Result>Success</Result><Values/><Timestamp>1357818581</Timestamp></CLICKATELLSDK>'));

        $result = $transport->validateActivationSms($data);

        $expected = array(
            'result' => array(
                'status'    => 'success',
                'response'  => ''
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Ensures that "get_list_callback" calling and replying as expected
     *
     * @return boolean.
     */
    public function testGetListCallback()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array("action" => "get_list_callback")));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3CAction%3Eget_list_callback%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_callback</Action><Result>Success</Result><Values><Value><callback_type_id>0</callback_type_id><callback_type>HTTP Get</callback_type></Value><Value><callback_type_id>1</callback_type_id><callback_type>HTTP Post</callback_type></Value><Value><callback_type_id>2</callback_type_id><callback_type>XML Post</callback_type></Value><Value><callback_type_id>3</callback_type_id><callback_type>XML Get</callback_type></Value><Value><callback_type_id>5</callback_type_id><callback_type>SOAP Get</callback_type></Value><Value><callback_type_id>6</callback_type_id><callback_type>SOAP Post</callback_type></Value></Values><Timestamp>1357839726</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListCallback();

        $expected = array(
            array(
                'callback_type_id'  => '0',
                'callback_type'     => 'HTTP Get'
            ),
            array(
                'callback_type_id'  => '1',
                'callback_type'     => 'HTTP Post'
            ),
            array(
                'callback_type_id'  => '2',
                'callback_type'     => 'XML Post'
            ),
            array(
                'callback_type_id'  => '3',
                'callback_type'     => 'XML Get'
            ),
            array(
                'callback_type_id'  => '5',
                'callback_type'     => 'SOAP Get'
            ),
            array(
                'callback_type_id'  => '6',
                'callback_type'     => 'SOAP Post'
            ),
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Ensures that "get_list_connection" calling and replying as expected
     *
     * @return boolean.
     */
    public function testGetListConnection()
    {
        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array(
                    "action"            => "get_list_connection",
                )
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3CAction%3Eget_list_connection%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>get_list_connection</Action><Result>Success</Result><Values><Value><connection_type_id>12</connection_type_id><connection_type>COM OBJECT API</connection_type></Value><Value><connection_type_id>7</connection_type_id><connection_type>FTP API</connection_type></Value><Value><connection_type_id>2</connection_type_id><connection_type>HTTP API</connection_type></Value><Value><connection_type_id>9</connection_type_id><connection_type>SMTP API</connection_type></Value><Value><connection_type_id>14</connection_type_id><connection_type>SOAP API</connection_type></Value><Value><connection_type_id>13</connection_type_id><connection_type>XML API</connection_type></Value></Values><Timestamp>1357840239</Timestamp></CLICKATELLSDK>'));

        $result = $transport->getListConnection();

        $expected = array(
            array(
                'connection_type_id'    => '12',
                'connection_type'       => 'COM OBJECT API',
            ),
            array(
                'connection_type_id'    => '7',
                'connection_type'       => 'FTP API',
            ),
            array(
                'connection_type_id'    => '2',
                'connection_type'       => 'HTTP API',
            ),
            array(
                'connection_type_id'    => '9',
                'connection_type'       => 'SMTP API',
            ),
            array(
                'connection_type_id'    => '14',
                'connection_type'       => 'SOAP API',
            ),
            array(
                'connection_type_id'    => '13',
                'connection_type'       => 'XML API',
            ),
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the create_connection call
     *
     * @return array
     */
    public function testCreateConnection()
    {
        $data = array(
            "user"                  => "test.test",
            "password"              => "password",
            "connection_id"         => 2,
            "api_description"       => "Something",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "create_connection",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3Cconnection_id%3E2%3C%2Fconnection_id%3E%3Capi_description%3ESomething%3C%2Fapi_description%3E%3CAction%3Ecreate_connection%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>create_connection</Action><Result>Success</Result><Values><Value><api_id>3407892</api_id></Value></Values><Timestamp>1357840983</Timestamp></CLICKATELLSDK>'));

        $result = $transport->createConnection($data);

        $expected = array(
            'api_id' => '3407892',
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Test the buy_credit_url call
     *
     * @return array
     */
    public function testBuyCreditUrl()
    {
        $data = array(
            "user"                  => "test.test",
            "password"              => "password",
        );

        $request = $this->getMockBuilder("Clickatell\Component\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(
                array_merge($data, array(
                    "action"            => "buy_credits_url",
                ))
            ));

        $transfer = $this->getMock(
            "Clickatell\Component\Transfer\TransferInterface"
        );

        $transport = new TransportConnect($transfer, $request, "abc123");

        $transfer->expects($this->any())
            ->method("execute")
            ->with(
                $this->equalTo($transport->getUrl()), 
                'XML=%3C%3Fxml+version%3D%221.0%22%3F%3E%0A%3Cclickatellsdk%3E%3Cuser%3Etest.test%3C%2Fuser%3E%3Cpassword%3Epassword%3C%2Fpassword%3E%3CAction%3Ebuy_credits_url%3C%2FAction%3E%3C%2Fclickatellsdk%3E%0A'
            )
            ->will($this->returnValue('<?xml version="1.0"?><CLICKATELLSDK><Action>buy_credits_url</Action><Result>Success</Result><Values><Value><buy_url>https://central.clickatell.com/central/buy_now.php?auth_id=xxxx</buy_url></Value></Values><Timestamp>1357841344</Timestamp></CLICKATELLSDK>'));

        $result = $transport->createConnection($data);

        $expected = array(
            'buy_url' => 'https://central.clickatell.com/central/buy_now.php?auth_id=xxxx',
        );

        $this->assertSame($expected, $result);
    }
}