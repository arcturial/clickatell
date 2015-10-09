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

/**
 * $_SESSION storage.
 *
 * @package  Clickatell\Otp
 * @author   Chris Brand <chris@cainsvault.com>
 */
class SessionStorage implements StorageInterface
{
    const PREFIX = 'otp.storage';

    /**
     * Construct a $_SESSION storage
     */
    public function __construct($force = false)
    {
        if ($force) session_start();
    }

    /**
     * Generate a lookup key.
     *
     * @param string $key The key to append
     *
     * @return string
     */
    private function key($key)
    {
        return self::PREFIX . '.' . $key;
    }

    /**
     * {@inheritdoc}
     */
    public function stash($to, $token)
    {
        $_SESSION[$this->key($to)] = $token;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($to)
    {
        if (isset($_SESSION[$this->key($to)])) unset($_SESSION[$this->key($to)]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($to)
    {
        return isset($_SESSION[$this->key($to)]) ? $_SESSION[$this->key($to)] : false;
    }
}
