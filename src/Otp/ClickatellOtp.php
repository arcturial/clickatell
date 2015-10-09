<?php
/**
 * The Clickatell SMS Library provides a standardised way of talking to and
 * receiving replies from the Clickatell API's.
 *
 * PHP Version 5.3
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */

namespace Clickatell\Otp;

use Clickatell\Clickatell;
use \Exception;

/**
 * This class generates and verifies one-time-pins delivered
 * by the Clickatell message component.
 *
 * @package  Clickatell\Otp
 * @author   Chris Brand <chris@cainsvault.com>
 */
class ClickatellOtp
{
    private $clickatell;
    private $storage;
    private $message = "Your verification password is %s";

    /**
     * Construct a new OTP handler.
     *
     * @param Clickatell       $clickatell The clickatell message component
     * @param StorageInterface $storage    The storage engine
     */
    public function __construct(Clickatell $clickatell, StorageInterface $storage)
    {
        $this->clickatell = $clickatell;
        $this->storage = $storage;
    }

    /**
     * Generate a new OTP token.
     *
     * @return string
     */
    protected function generateToken()
    {
        $length = 7;
        $token = "";
        $vowels = 'aeuyAEUY';
        $consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ123456789';

        for ($i = 1; $i <= $length; $i++)
        {
            $use = ($i % 2) ? $consonants : $vowels;
            $token .= $use[(rand() % strlen($use))];
        }

        return $token;
    }

    /**
     * Generate a new message.
     *
     * @param string $token The token to add to message
     *
     * @return string
     */
    protected function generateMessage($token)
    {
        return sprintf($this->message, $token);
    }

    /**
     * Override the default OTP message. The first occurence
     * of %s will be replaced by the token.
     *
     * @param string $message The message to send.
     *
     * @return ClickatellOtp
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Send a newly generated PIN.
     *
     * @param string $to     The number to send the PIN to
     * @param string $unique The improve security, you can specify a unique reference
     *
     * @return string
     */
    public function sendPin($to, $unique = null)
    {
        $token = $this->generateToken();
        $generated = $this->generateMessage($token);
        $messages = $this->clickatell->sendMessage($to, $generated);
        $message = current($messages);
        $id = $message->id;

        if (!$id) throw new Exception($message->error, $message->errorCode);
        $key = md5($to . ($unique ? $unique : ''));
        $this->storage->stash($key, $token);
        return $id;
    }

    /**
     * Verify a PIN based on the message ID it was originally
     * stashed against.
     *
     * @param string $to     The number
     * @param string $token  The token to verify
     * @param string $unique The unique ref used when generating the token
     *
     * @return boolean
     */
    public function verifyPin($to, $token, $unique = null)
    {
        $key = md5($to . ($unique ? $unique : ''));

        if ($this->storage->get($key) == $token)
        {
            $this->storage->delete($key);
            return true;
        }

        return false;
    }
}
