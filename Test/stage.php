<?php
/**
 * Callback example usage.
 *
 * Copy this script to your webhost
 * and hit the host with this request.
 *
 * http://www.yoursite.com/stage.php?apiMsgId=1234&cliMsgId=&to=27841234567&timestamp=&from&status=6&charge=0.1
 */

// Change this path to Clickatell location or the composer autoloader
require_once __DIR__ . "/../Clickatell.php";

use Clickatell\Clickatell;

Clickatell::parseCallback(function ($values) {

    // Please note, the callback will only trigger
    // if all these parameters are set in the $_GET
    // global. The clickatell API suggests that these
    // are the parameters we will receive on callback.
    //
    // apiMsgId,cliMsgId,to,timestamp,from,status,charge=0.1
    var_dump($values);
});