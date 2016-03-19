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

namespace Clickatell;

use \Exception;

/**
 * This class provides helper functionality to parse Clickatell MT and MO callbacks.
 * The callback functional will only trigger if the callback contains the relevant parameters.
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class Callback
{
    /**
     * @var string
     */
    const ERR_NOT_TRUSTED = 'The callback originated from an IP that is not trusted. (%s)';

    /**
     * Enable this setting if you want to restrict callbacks that come
     * from an unknown source.
     *
     * @var boolean
     */
    public static $restrictIP = false;

    /**
     * The list of IPs that are known. Clickatell public IPs.
     *
     * @var array
     */
    public static $allowedIPs = array("196.216.236.2");

    /**
     * Triggers if a clickatell MT callback has been received by the page.
     *
     * @param callable $callable The callable function
     *
     * @return boolean
     */
    public static function parseCallback($callable)
    {
        $callingIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        if (static::$restrictIP && !in_array($callingIP, static::$allowedIPs)) throw new Exception(sprintf(self::ERR_NOT_TRUSTED, $callingIP));

        $required = array_flip(
            array(
                'apiMsgId',
                'cliMsgId',
                'to',
                'timestamp',
                'from',
                'status',
                'charge'
            )
        );

        $values = array_intersect_key($_GET, $required);
        $diff = array_diff_key($required, $values);

        // If there are no difference, then it means the callback
        // passed all the required values.
        if (empty($diff)) {
            return call_user_func_array($callable, array($values));
        }

        return false;
    }

    /**
     * Triggers if a clickatell MO callback has been received by the page.
     *
     * @param callable $callable The callable function
     *
     * @return boolean
     */
    public static function parseMoCallback($callable)
    {
        $callingIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        if (static::$restrictIP && !in_array($callingIP, static::$allowedIPs)) throw new Exception(sprintf(self::ERR_NOT_TRUSTED, $callingIP));

        $required = array_flip(
            array(
                'api_id',
                'moMsgId',
                'from',
                'to',
                'timestamp',
                'text'
            )
        );

        $optional = array_flip(
            array(
                'charset',
                'udh',
                'network'
            )
        );

        $values = array_intersect_key($_GET, $required);
        $diff = array_diff_key($required, $values);

        // Grab optional values too
        $values = array_merge($values, array_intersect_key($_GET, $optional));

        // If there are no difference, then it means the callback
        // passed all the required values.
        if (empty($diff)) {
            return call_user_func_array($callable, array($values));
        }

        return false;
    }
}