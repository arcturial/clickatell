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
 * @package  Clickatell\Test
 * @author   Chris Brand <chris@cainsvault.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/arcturial
 */

/**
 * Auto loaded for loading Clickatell Libraries into the
 * test cases.
 */
$autoload = function ($class) {

    // Load custom Clickatell classes
    $include = __DIR__ . "/../../" . $class . ".php";
    
    if (is_file($include)) {
        include_once $include;  
    }   
};

spl_autoload_register($autoload);