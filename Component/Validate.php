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

use Clickatell\Exception\ValidateException as ValidateException;

/**
 * This is the Validation class. Some API fields might require validation
 * or just warnings. So let's try and minimize the amount of errors we might get
 * from the API.
 *
 * @category Clickatell
 * @package  Clickatell\Component
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */
class Validate
{
    /**
     * API call meta validation information
     * 
     * Available definitions:
     * - telephone
     * - int
     * - require
     *
     * @var array
     */
    private static $_meta = array(
        'sendMessage' => array(
            'to' => 'required|telephone',
            'from' => 'telephone'
        ),
        'getBalance' => array(
        ),
        'queryMessage' => array(
            'apiMsgId' => 'required'
        ),
        'routeCoverage' => array(
            'msisdn' => 'telephone'
        ),
        'getMessageCharge' => array(
            'apiMsgId' => 'required'
        )
    );

    /**
     * Process a value to ensure it's an int.
     *
     * @param mixed $value Value to process
     *
     * @return boolean
     * @throws Clickatell\Exception\ValidationException
     */
    private static function _validateInt($value)
    {
        if (!is_numeric($value)) {
            throw new ValidateException(
                ValidateException::ERR_INVALID_INT . " (" . $value . ")"
            );
        }

        return true;
    }

    /**
     * Process a value to check if the value
     * actually exist and is not just blank.
     *
     * @param mixed $value Value to process
     *
     * @return boolean
     * @throws Clickatell\Exception\ValidationException
     */
    private static function _validateRequired($value  = '')
    {
        if (empty($value) || $value == '' || $value == null) {
            throw new ValidateException(
                ValidateException::ERR_FIELD_REQUIRED
            );
        }

        return true;
    }

    /**
     * Process a value to check if it's a valid mobile
     * number.
     *
     * @param mixed $value Value to process
     *
     * @return boolean
     * @throws Clickatell\Exception\ValidationException
     */
    private static function _validateTelephone($value)
    {
        $first = substr($value, 0, 1);

        if ($first == '0') {
            // Raise a warning
            trigger_error(
                "ClickatellValidation: '" . $value . "'"
                . " replacing leading zero's is advised.",
                E_USER_WARNING
            );
        }

        // Catch any errors and forward them on
        try {
            
            static::_validateInt($value);

        } catch (ValidateException $exception) {

            throw new ValidateException(
                ValidateException::ERR_INVALID_TELEPHONE . " (" . $value . ")"
            );    
        }

        return true;
    }

    /**
     * Traverse a value and assert a condition on
     * all it's entries.
     *
     * @param mixed    $value    Value to check
     * @param function $function Function to validate with
     *
     * @return boolean
     */
    private static function _traverse($value, $function)
    {
        // If it's an array, loop it
        if (is_array($value)) {

            foreach ($value as $val) {
                static::_traverse($val, $function);
            }

        } else {

            return static::$function($value);
        }
    }   

    /**
     * Runs all validation checks against a certain value.
     * Any exceptions found are forwarded along.
     *
     * @param mixed $value      Value to check
     * @param array $validation All validation checks to run
     *
     * @return boolean
     */
    private static function _runChecks($value, $validation)
    {
        // Loop through the defined asserts
        foreach ($validation as $assert) {

            $method = "_validate" . ucfirst($assert);
            
            // Catch any failures and forward them
            // along with the failing key.
            if (method_exists('Clickatell\Component\Validate', $method)) {
                static::_traverse($value, $method);
            }
        }   

        return true;
    }



    /**
     * Process a packet based on the method requested.
     *
     * @param string $method Method to process
     * @param array  $packet Packet to validate
     *
     * @return boolean
     */
    public static function processValidation($method, $packet)
    {
        // Valid method?
        if (isset(static::$_meta[$method])) {

            $meta = static::$_meta[$method];

            // Only expects one dimension
            foreach ($packet as $key => $val) {

                // Check if validation is registered
                if (isset($meta[$key])) {

                    $validations = explode("|", $meta[$key]);

                    if (in_array('required', $validations) || $val != '') {

                        // Catch any failures and forward them
                        // along with the failing key.
                        try {

                            static::_runChecks($val, $validations);

                        } catch (ValidateException $exception) {

                            throw new ValidateException(
                                "Parameter '" . $key . "' - " 
                                . $exception->getMessage()
                            );
                        }
                    }
                }
            }
        }
    }
}