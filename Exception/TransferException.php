<?php
namespace Clickatell\Exception;

use \Exception as Exception;

/**
 * This is the custom exception handler for Transfer exceptions.
 *
 * @uses \Exception
 * @package Clickatell\Exception
 * @author Chris Brand
 */
class TransferException extends Exception
{
    /**
	 * The Transfer handler encountered a problem.
	 * @var string
	 */
    const ERR_HANLDER_EXCEPTION   = "Handler Encountered a Problem";   
}