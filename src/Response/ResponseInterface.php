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

namespace Clickatell\Response;

/**
 * This class represents a response object.
 *
 * @package  Clickatell\Response
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface ResponseInterface
{
    /**
     * Get the error description
     *
     * @return string
     */
    public function getError();

    /**
     * Get the error code
     *
     * @return int
     */
    public function getErrorCode();

    /**
     * Check if this response is an error
     *
     * @return boolean
     */
    public function isError();
}