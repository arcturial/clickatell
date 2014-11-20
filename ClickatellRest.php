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
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell;

use Clickatell\Component\Translate\TranslateJson as TranslateJson;
use Clickatell\Api\Api as Api;
use Clickatell\Exception\ApiException as ApiException;
use \Closure as Closure;
use Clickatell\Component\Event as Event;
use Clickatell\Component\Validate as Validate;
use \LogicException;

/**
 * This is the main messenger class that encapsulates various objects to succesfully
 * send Clickatell calls and respond in an appropriate manner. The messenger class
 * enables you to set your own Transport and Translate interfaces.
 *
 * @category Clickatell
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class ClickatellRest extends Clickatell
{
    /**
     * The REST Transport Interface
     * @var string
     */
    const REST_API = "Clickatell\Api\Rest";

    /**
     * Clickatell Messenger Instantiation. Creates the Transport/Translate/Request
     * interfaces required.
     *
     * @param string $token Clickatell auth token
     *
     * @return boolean
     */
    public function __construct($token)
    {
        // Register autoloader
        spl_autoload_register(array($this, '_autoLoad'));

        // Create transport
        $transport = self::REST_API;

        $this->_transport = new $transport(new TranslateJson());
        $this->_transport->authenticateByToken($token);

        // Clear all registered events
        Event::clear();

        // Add validation listener using events
        Event::on(
            'request',
            function ($data) {

                $method = $data['call'];
                $args = $data['request'];

                Validate::processValidation($method, $args);
            }
        );
    }
}