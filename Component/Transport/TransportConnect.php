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
 * @package  Clickatell\Component\Transport
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Transport;

use Clickatell\Component\Request as Request;
use Clickatell\Component\Transport as Transport;
use Clickatell\Exception\Diagnostic as Diagnostic;
use Clickatell\Component\Transport\TransportInterfaceExtract as TransportInterfaceExtract;
use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Component\Transfer\TransferInterface as TransferInterface;
use Clickatell\Component\Transport\Packet\PacketXml as PacketXml;
use Clickatell\Component\Validate as Validate;
use Clickatell\Exception\ValidateException as ValidateException;

/**
 * This is the Connect XML interface to Clickatell. It wraps requests
 * into XML formatted blobs and handles the response from Clickatell
 * into a generic format.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transport
 * @author   Thomas Shone <xsist10@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class TransportConnect extends Transport implements TransportInterfaceExtract
{
    /**
     * CONNECT endpoint
     * @var string
     */
    const CONNECT_ENDPOINT = "https://connect.clickatell.com/";

    /**
     * The token to append to the connect endpoint
     * @var string
     */
    private $_token;

    /**
     * Overload to capture the token
     *
     * @param Clickatell\Component\Transfer\TransferInterface   $transfer Transfer interface to use
     * @param Clickatell\Component\Request                      $request  Request object to process
     * @param string                                            $token
     *
     * @return boolean
     */
    public function __construct(TransferInterface $transfer, Request $request, $token)
    {
        $this->_token = $token;
        parent::__construct($transfer, $request);
    }

    /**
     * Recursive XML extractor to handle nested XML
     *
     * @param   \SimpleXmlIterator  $xml
     * @return  array
     */
    private function __extract(\SimpleXmlIterator $xml)
    {
        $result = array();
        foreach ($xml as $elementName => $node) {
            if ($node->count())
            {
                foreach ($node as $element)
                {
                    if ($node->count() == 1)
                    {
                        $result[$elementName] = $this->__extract($element);
                    }
                    else
                    {
                       $result[$elementName][] = $this->__extract($element);
                    }
                }
            }
            else
            {
                $result[$elementName] = (string) $node;
            }
        }

        return $result;
    }

    /**
     * Generic handling of Connect API results
     *
     * @param   array   $result
     * @return  array
     */
    protected function ___handleResult($result)
    {
        // Check if it's a failure
        if ($result['Result'] == "Error") {

            return $this->wrapResponse(
                Transport::RESULT_FAILURE, 
                (string) $result['Description']
            );
        } else {
            if (!empty($result['Values']))
            {
                return $result['Values']; 
            }
            else
            {
                return $this->wrapResponse(
                    Transport::RESULT_SUCCESS,
                    ''
                );
            }
        }
    }

    /**
     * Generic request building
     *
     * @param   array   $data
     * @param   array   $fields
     */
    protected function ___buildRequest($data, $fields)
    {
        foreach ($fields as $field)
        {
            if (isset($data[$field]))
            {
                $this->request()->$field = $data[$field];
            }
        }
    }

    /**
     * Extracts the result from the framework into an associative
     * array.
     *
     * @param string $response Response string from API
     *
     * @return array
     */
    public function extract($response)
    {
        $iterator = new \SimpleXMLIterator($response);
        $iterator->rewind();
        return $this->__extract($iterator);
    }

    /**
     * This builds the xml packet from the Request object
     * parameters.
     *
     * @param Clickatell\Component\Request $request Request object to process
     *
     * @return string
     */
    public function buildPost(Request $request)
    {
        $params = $request->getParams();
        $params['Action'] = $params['action'];
        unset($params['action']);

        // Build the XML request packet
        $packet = new PacketXml("clickatellsdk");
        $xml = $packet->toXml($params);
        
        return "XML=" . urlencode($xml);
    }

    /**
     * Get the URL end point
     *
     * @return  string
     */
    public function getUrl()
    {
        return self::CONNECT_ENDPOINT . $this->_token;
    }

    /**
     * This service call returns all country names and internal identification
     * numbers
     *
     * @return array
     */
    public function getListCountry()
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_list_country";

        $response = $this->transfer()->execute(
            self::CONNECT_ENDPOINT . $this->_token, 
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call returns all country dial prefixes and internal
     * identification numbers.
     *
     * @return array
     */
    public function getListCountryPrefix()
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_list_country_prefix";

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call returns a list of valid Clickatell account types (e.g.
     * International or USA Local) and their respective ID.
     *
     * @return array
     */
    public function getListAccount()
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_list_account";

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call returns country specific terms and conditions based on
     * the country selection. If there are no country specific terms and
     * conditions the system returns the main version.
     *
     * @param   string  $ip_address (optional. Either $ip_address or $country_id is required)
     * @param   integer $country_id (optional. Either $ip_address or $country_id is required)
     * @return  array
     */
    public function getListTerms($ip_address, $country_id = 0)
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_list_terms";

        if ($ip_address)
        {
            $this->request()->client_ip_address = $ip_address;
        }
        if ($country_id)
        {
            $this->request()->country_id = $country_id;
        }

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->___handleResult($this->extract($response));

        if (!empty($result['URL_location']))
        {
            $result['Terms'] = file_get_contents($result['URL_location']);
        }
        return $result;
    }

    /**
     * This service call allows the application or website to register new
     * Clickatell Central accounts which includes all the standard requirements.
     *
     * @param   array    $data
     * @return  boolean
     */
    public function register($data)
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "register";

        $required_fields = array(
            'user',
            'fname',
            'sname',
            'password',
            'email_address',
            'mobile_number',
            'country_id',
            'captcha_code',
            'captcha_id'
        );
        Validate::validateRequired($data, $required_fields);

        // The Country Field is not a number. Check if it matches a country name
        if (!is_numeric($data['country_id']))
        {
            $country_list = $this->getListCountry();
            foreach ($country_list as $country)
            {
                if ($country['name'] == $data['country_id'])
                {
                    $data['country_id'] = $data['country_id'];
                }
            }
        }

        $fields = array_merge(array(
            'account_id',
            'company',
            'coupon_code',
            'activation_redirect',
            'weekly_update',
            'email_format',
            'test_mode',
        ), $required_fields);
        $this->___buildRequest($data, $fields);
        
        $this->request()->accept_terms = 1;
        $this->request()->force_create = 1;

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call allows the application or website to resend the
     * activation email containing the activation URL. As an option, the command
     * allows for the modification of the activation email address.
     *
     * @param   array   $data
     * @return  array
     */
    public function resendEmailActivation($data = array())
    {
        $required_fields = array(
            'user',
            'password',
            'email_address',
        );
        Validate::validateRequired($data, $required_fields);
        $this->___buildRequest($data, $required_fields);

        $this->request()->action = 'resend_email_activation';

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call allows the application or website to validate a user
     * login.
     *
     * @param   array   $data
     * @return  array
     */
    public function authenticateUser($data = array())
    {
        $required_fields = array(
            'user',
            'password',
        );
        Validate::validateRequired($data, $required_fields);
        $this->___buildRequest($data, $required_fields);

        $this->request()->action = 'authenticate_user';

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call allows the application or website to request Clickatell
     * to generate a new password using the existing ‘forgot password’
     * functionality.
     *
     * @param   array   $data
     * @return  array
     */
    public function forgotPassword($data = array())
    {
        $required_fields = array(
            'user',
            'email_address',
            'captcha_code',
            'captcha_id'
        );
        Validate::validateRequired($data, $required_fields);
        $this->___buildRequest($data, $required_fields);

        $this->request()->action = 'forgot_password';

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call returns a random captcha image in PNG format. The
     * returned image must be URL decoded and rendered before it can be
     * displayed.
     *
     * @return  string  RAW PNG image
     */
    public function getCaptcha()
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_captcha";

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        $result = $this->___handleResult($this->extract($response));
        $result['captcha_image'] = urldecode($result['captcha_image']);
        return $result;
    }

    /**
     * The sms_activation_status service can be called to check if a user’s
     * account has been SMS activated.
     *
     * @param   array   $data
     * @return  array
     */
    public function smsActivationStatus($data = array())
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "sms_activation_status";

        $required_fields = array(
            'user',
            'password',
        );
        Validate::validateRequired($data, $required_fields);

        $fields = array_merge(array(
            'captcha_id',
            'captcha_code',
        ), $required_fields);
        $this->___buildRequest($data, $fields);

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call allows the application or website to send the SMS
     * activation code to the mobile number stored with the Clickatell account.
     *
     * @param   array   $data
     * @return  array
     */
    public function sendActivationSms($data = array())
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "send_activation_sms";

        $required_fields = array(
            'user',
            'password',
        );
        Validate::validateRequired($data, $required_fields);

        $fields = array_merge(array(
            'captcha_id',
            'captcha_code',
        ), $required_fields);
        $this->___buildRequest($data, $fields);

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call allows the application or website to SMS activate a
     * Clickatell account.
     *
     * @param   array   $data
     * @return  array
     */
    public function validateActivationSms($data = array())
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "validate_activation_sms";

        $required_fields = array(
            'user',
            'password',
            'sms_activation_code',
        );
        Validate::validateRequired($data, $required_fields);

        $fields = array_merge(array(
            'captcha_id',
            'captcha_code',
        ), $required_fields);
        $this->___buildRequest($data, $fields);

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call returns a list of valid MT callback types (methods)
     * (e.g. HTTP GET, HTTP POST) and their respective ID’s.
     *
     * @return  array
     */
    public function getListCallback()
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_list_callback";

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        return $this->___handleResult($this->extract($response));
    }

    /**
     * This service call returns a list of valid Clickatell connection types
     * (e.g. HTTP API, SMTP API) and their respective ID’s
     *
     * @return  array
     */
    public function getListConnection()
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "get_list_connection";

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        // Convert response into associative array
        return $this->___handleResult($this->extract($response));
    }

    /**
     * The create_connection service call allows a user to add new messaging API
     * connections to their accounts through your application or website.
     *
     * @param   array   $data
     * @return  array
     */
    public function createConnection($data = array())
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "create_connection";

        $required_fields = array(
            'user',
            'password',
        );
        Validate::validateRequired($data, $required_fields);

        $fields = array_merge(array(
            'captcha_id',
            'captcha_code',
            'connection_id',
            'ftp_password',
            'api_description',
            'ip_address',
            'Dial_prefix',
            'callback_url',
            'callback_type_id',
            'callback_username',
            'callback_password',
        ), $required_fields);
        $this->___buildRequest($data, $fields);

        $response = $this->transfer()->execute(
            $this->getUrl(), 
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    /**
     * The buy_credits_url service call creates a hyperlink that will go to the
     * user’s Clickatell account and allow them to buy credits.
     *
     * @param array $data
     * @return array
     * @throws ConnectApiException
     */
    public function buyCreditsUrl($data = array())
    {
        $this->request()->reset(); // clean request
        $this->request()->action = "buy_credits_url";

        $required_fields = array(
            'user',
            'password',
        );
        Validate::validateRequired($data, $required_fields);

        $fields = array_merge(array(
            'captcha_id',
            'captcha_code',
        ), $required_fields);
        $this->___buildRequest($data, $fields);

        $response = $this->transfer()->execute(
            $this->getUrl(),
            $this->buildPost($this->request())
        );

        return $this->___handleResult($this->extract($response));
    }

    public function sendMessage($to, $message, $from = "", $callback = true) {}
    public function getBalance() {}
    public function queryMessage($apiMsgId) {}
    public function routeCoverage($msisdn) {}
    public function getMessageCharge($apiMsgId) {}
}