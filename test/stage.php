<?php
/**
 * This file is purely for demonstration purposes. It contains some examples of
 * how to use the library.
 */
require __DIR__ . "/../vendor/autoload.php";

use Clickatell\Api\ClickatellHttp;
use Clickatell\Api\ClickatellRest;

/**
 * Example usage for the Clickatell HTTP API.
 * ==========================================
 */
$http = new ClickatellHttp([user], [password], [apiId]);

// Replace these numbers with test numbers
$messages = $http->sendMessage(array([number]), 'Test Message', array());

// Check the cost per message
foreach ($messages as $message) {
    if (!$message->error) {
        $charge = $http->getMessageCharge($message->id);
        var_dump("status: " . $charge->status);
        var_dump("description: " . $charge->description);
        var_dump("charge: " . $charge->charge);
    } else {
        var_dump("error: " . $message->error);
        var_dump("errorCode: " . $message->errorCode);
    }

}

// Check the account balance
$balance = $http->getBalance();
var_dump("balance: " . $balance->balance);

/**
 * Example usage for the Clickatell REST API.
 * ==========================================
 */
$rest = new ClickatellRest([token]);

// Replace these numbers with test numbers
$messages = $rest->sendMessage(array([number]), 'Test Message', array());

// Check the cost per message
foreach ($messages as $message) {
    if (!$message->error) {
        $charge = $rest->getMessageCharge($message->id);
        var_dump("status: " . $charge->status);
        var_dump("description: " . $charge->description);
        var_dump("charge: " . $charge->charge);
    } else {
        var_dump("error: " . $message->error);
        var_dump("errorCode: " . $message->errorCode);
    }

}

// Check the account balance
$balance = $rest->getBalance();
var_dump("balance: " . $balance->balance);