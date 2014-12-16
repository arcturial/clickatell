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

/**
 * This is the HTTPS interface to Clickatell. It transforms
 * the Request object into a suitable query string and
 * handles the response from Clickatell.
 *
 * @category Clickatell
 * @package  Clickatell\Api
 * @author   Ole Rößner <oroessner@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 * @uses     Clickatell\Component\Transport
 */
class Https extends Http
{
    protected $baseUrl = 'https://api.clickatell.com';
}
