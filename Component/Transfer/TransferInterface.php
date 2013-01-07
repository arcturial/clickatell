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
 * @package  Clickatell\Component\Transfer
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Transfer;

use Clickatell\Component\Request as Request;

/**
 * This is the Transfer interface. Any new Transfer handlers need to
 * implement this to ensure that functionality stays in tact.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Transfer
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface TransferInterface
{
    /**
     * Executes the Transfer interface.
     *
     * @param string $url   URL to execute
     * @param array  $param Parameters to pass to URL
     *
     * @return string
     */
    public function execute($url, $param);
}