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
 * @package  Clickatell\Component\Translate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Translate;

use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;

/**
 * This is the JSON Translater. It takes a response from the Transport
 * and translates them into your requested format. In this case...JSON
 *
 * @category Clickatell
 * @package  Clickatell\Component\Translate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class TranslateJson implements TranslateInterface
{
    /**
     * Translates an array to JSON
     *
     * @param array $response Array to translate
     *
     * @return string
     */
    public function translate(array $response)
    {
        return json_encode($response);
    }
}