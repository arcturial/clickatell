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
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Api;

use Clickatell\Api\Definition\ApiBulkInterface as ApiBulkInterface;
use Clickatell\Exception\Diagnostic as Diagnostic;
use Clickatell\Exception\TransferException as TransferException;

/**
 * This is the SMTP interface to Clickatell. It transforms
 * the Request object into a email message.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class Smtp extends Api implements ApiBulkInterface
{
    /**
     * Email address to send messages
     * @var string
     */
    const EMAIL_ENDPOINT = "sms@messaging.clickatell.com";

    /**
     * Do an email request to the Clickatell API
     *
     * @param string $url    URL to call
     * @param array  $packet Packet to process
     *
     * @return string
     */
    protected function callApi($url, array $packet)
    {
        $content = "";

        foreach ($packet as $key => $val) {
            $content .= $key . ":" . $val . "\r\n";
        }   

        // From address doesn't really matter
        $headers = 'From: request@domain.com' . '\r\n';
        $headers .= 'Content-type: text/plain; charset=utf-8' . '\r\n';

        if (!mail(self::EMAIL_ENDPOINT, '', $content, $headers)) {

            throw new TransferException(TransferException::ERR_MAIL_UNDELIVERED);
        }

        return true;
    }

    /**
     * The "sendMsg" SMTP call. Builds up the request and handles the response
     * from Clickatell. Unfortunately we have to fake the response. We can't
     * retrieve the API Message ID from the SMTP call.
     *
     * @param array   $to       Recipient list
     * @param string  $message  Message
     * @param string  $from     From address (sender ID)
     * @param boolean $callback Use callback or not
     *
     * @return array
     */
    public function sendMessage(array $to, $message, $from = "", $callback = true)
    {      
        // Grab auth out of the session
        $packet['user'] = $this->auth['user'];  
        $packet['password'] = $this->auth['password'];  
        $packet['api_id'] = $this->auth['api_id'];  

        // Build data packet
        $packet['to'] = implode(",", $to);
        $packet['text'] = $message;
        $packet['from'] = $from;
        $packet['callback'] = $callback;

        $result = $this->callApi('http://api.clickatell.com/http/sendmsg', $packet);

        $result = $this->extract($result);

        if (!isset($result['ERR'])) {

            $packet = array();
            $packet['apiMsgId'] = "-1";

            return $this->wrapResponse(Api::RESULT_SUCCESS, $packet); 

        } else {

            return $this->wrapResponse(Api::RESULT_FAILURE, $result['ERR']);
        }
    }
}