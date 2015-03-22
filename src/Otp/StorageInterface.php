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
 * The storage component is responsible for keeping the
 * generated tokens in a cache for verification.
 *
 * @package  Clickatell\Otp
 * @author   Chris Brand <chris@cainsvault.com>
 */
interface StorageInterface
{
    /**
     * Stash a specific token against a message ID.
     *
     * @param string $apiMsgId The message ID
     * @param string $token    The token to associate
     *
     * @return boolean
     */
    public function stash($apiMsgId, $token);

    /**
     * Delete a specific message association.
     *
     * @param string $apiMsgId The message ID
     *
     * @return boolean
     */
    public function delete($apiMsgId);

    /**
     * Get a specific message ID token.
     *
     * @return string
     */
    public function get($apiMsgId);
}