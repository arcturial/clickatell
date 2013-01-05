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
 * @package  Clickatell\Component\Tranlate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
namespace Clickatell\Component\Translate;

use Clickatell\Component\Request as Request;

/**
 * This is the Translate interface. The interface ensures
 * that all new Translaters include these functions.
 *
 * @category Clickatell
 * @package  Clickatell\Component\Tranlate
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
interface TranslateInterface
{
    /**
     * Translate function accepts an array and outputs a mixed response
     * depending on the Translater.
     *
     * @param array $response Array to translate
     *
     * @return mixed
     */
    public function translate(array $response);
}