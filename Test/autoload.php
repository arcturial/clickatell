<?php
/**
 * Tells the testsuite where to fine the Clickatell classes
 * used to run tests against.
 *
 * @package Clickatell\Test
 * @author Chris Brand
 */
spl_autoload_register(function($class) {

    #-> Load custom Clickatell classes
    $include = __DIR__ . "/../../" . $class . ".php";
    
    if (is_file($include))
    {
        require_once $include;  
    }   
});