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
 * @package  Clickatell\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component;

use \Closure as Closure;

/**
 * This is the Event class. Handles the triggering and observing
 * of registered events.
 *
 * @category Clickatell
 * @package  Clickatell\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class Event
{
    /**
     * Registered event listeners
     * @var array
     */
    private static $_listeners = array('request' => array(), 'response' => array());

    /**
     * Register a new event listener for a specific
     * event trigger.
     *
     * @param string   $event    Event to listen for
     * @param \Closure $callback The callback to trigger 
     *
     * @return boolean
     */
    public static function on($event, Closure $callback)
    {
        self::$_listeners[$event][] = $callback;
    }

    /**
     * Trigger a certain event's callbacks to execute
     *
     * @param string $event Event to trigger
     * @param mixed  $data  Data to pass to the handler
     *
     * @return boolean
     */
    public static function trigger($event, $data)
    {
        foreach (self::$_listeners[$event] as $callback) {
            // Execute
            $callback($data);
        }

        return true;
    }

    /**
     * Clear all current listeners.
     *
     * @return boolean
     */
    public static function clear()
    {
        self::$_listeners['request'] = array();
        self::$_listeners['response'] = array();

        return true;
    }
}