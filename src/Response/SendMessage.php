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

use Clickatell\Response\ResponseInterface;

/**
 * This class represents a response to the sendMessage call
 *
 * @package  Clickatell\Response
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class SendMessage implements ResponseInterface
{
    private $id, $to, $error, $code;

    public function __construct($id, $to, $error, $code)
    {
        $this->id = $id;
        $this->to = $to;
        $this->error = $error;
        $this->code = $code;
    }

    public function getApiMsgId()
    {
        return $this->id;
    }

    public function getDestination()
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function isError()
    {
        return (boolean) $this->error;
    }
}