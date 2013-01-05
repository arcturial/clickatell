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
use Clickatell\Component\Request as Request;

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
     * Validate a number to check if it will work
     * with the Clickatell API.
     *
     * @param int $number Number to validate
     *
     * @return boolean
     */
    private static function _validateLeadingZero($number)
    {
        if (substr($number, 0, 1) == "0") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate the input to be an int. Useful for API Id's and
     * so forth.
     *
     * @param int $number Number to validate
     *
     * @return boolean
     */
    private static function _validateInt($number)
    {
        if (is_numeric($number)) {
            return true;
        } else {
            return false;
        }       
    }    

    /**
     * Run a range of validation checks against
     * a telephone number.
     *
     * @param int $number Number to validate
     *
     * @return boolean
     * @throws Clickatell\Exception\ValidateException
     */
    private static function _validateTelephone($number)
    {
        if (self::_validateLeadingZero($number)) {
            trigger_error(
                __CLASS__ . ": " . ValidateException::ERR_LEADING_ZERO 
                . " (" . $number .")", 
                E_USER_NOTICE
            );
        }

        if (!self::_validateInt(trim($number))) {
            throw new ValidateException(ValidateException::ERR_INVALID_NUM);
        }

        return true;
    }

    /**
     * Validate the list of parameters.
     *
     * @param array $params Parameters to validate if possible
     *
     * @return boolean
     * @throws ValidateException
     */
    public static function validateParameters(array $params)
    {
        foreach ($params as $key => $val) {
            
            try {

                switch($key) {
                    case 'api_id':

                        // Make sure the api_id is an integer

                        if (!empty($val)) {
                            self::_validateInt($val);
                        }

                        break;

                    case 'to':

                        // Make sure the recipient list contains valid numbers

                        if (strpos($val, ",") !== false) {

                            // Recipient list has multiple entries
                            $split = explode(",", $val);
                            
                            foreach ($split as $offset => $number) {

                                try {

                                    self::_validateTelephone($number); 

                                } catch (ValidateException $exception) {
                                    throw new ValidateException(
                                        $exception->getMessage() 
                                        . " (number at offset " . $offset . ")"
                                    );
                                }
                            }

                        } else {

                            // Validate single entry
                            self::_validateTelephone($val);
                        }

                        break;

                    case 'from':
                        
                        // Ensure that the from address is a valid number

                        if (!empty($val)) {
                            self::_validateTelephone($val);
                        }

                        break;
                }

            } catch (ValidateException $exception) {
                throw new ValidateException(
                    "Parameter '" . $key ."' - " . $exception->getMessage()
                );
            }
        }
    }
}