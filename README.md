Clickatell SMS Messenger Library
================================

Master: [![Build Status](https://secure.travis-ci.org/arcturial/clickatell.png?branch=master)](http://travis-ci.org/arcturial/clickatell)

This library allows easy access to connecting the [Clickatell's](http://www.clickatell.com) different messenging API's.

### Table of Contents
* Installation
* Usage
* Supported API calls
* Events
* Callbacks


1. Installation
------------------

This library uses [composer](http://www.getcomposer.org) and can be acquired using the following in your composer.json file.

``` json
{
    "require": {
        "arcturial/clickatell": "*"
    }
}
```


2. Usage
------------------

The Clickatell library allows you specify several ways to connect to Clickatell. The current ones supported are HTTP and XML. These connections are called "Transports".

The default transport is HTTP.

``` php
$clickatell = new Clickatell($username, $password, $apiID);

$response = $clickatell->sendMessage(1111111111, "My Message");

// {"result":{"status":"success|false","response":[{"apiMsgId":"string|false","to":"xxxxxxxxxxx","error":"string|false"}]}}
```

The response you get back will be JSON (as indicated above) that will contain two keys (status, response). The 'response' key will be an array of messages and their message ID's (even if you just specified one number). The response for sending messages will always be an array so that consistency between different packets can be maintained.

You can specify a different output using the Clickatell constructor or using the setTransport() method.

``` php
$clickatell = new Clickatell($username, $password, $apiID, Clickatell::TRANSPORT_XML);

// OR

$clickatell = new Clickatell($username, $password, $apiID);

$clickatell->setTransport(new Clickatell\Component\Transport\TransportXml);
```

NOTE: The library uses name spaces, and the Clickatell messenger is located at `Clickatell\Clickatell`

3. Supported API calls
------------------

Clickatell has a couple of different API's that each support a subset of functions. We are going to refer to them as
Messaging and Bulk Messaging API's for this document.

### Messaging API's

The following are all messaging API's.

``` php
use Clickatell\Component\Transport\TransportHttp;

use Clickatell\Component\Transport\TransportSoap;

use Clickatell\Component\Transport\TransportXml;

use Clickatell\Component\Transport\TransportSmtp;
```

These Transports all support the following functions

``` php
sendMessage(array $to, string $message, $from = "", $callback = true, $extra = array());

getBalance();

queryMessage($apiMsgId);

routeCoverage($msisdn);

getMessageCharge($apiMsgId);
```

### Bulk Messaging API's

The following are bulk messaging API's. The have only a limited number of functions and are more suited for bulk messaging. Since they aren't processed in real time, these Transports do not
return the same results as the normal messaging API's.

``` php
use Clickatell\Component\Transport\TransportSMTP;
```

These Transports all support the following functions

``` php
sendMessage(array $to, string $message, $from = "", $callback = true, $extra = array());
```


4. Events
---------------

This library provides a couple of events to extend the ability of the API's. Current support events are `request` and `response`.

Example

``` php
<?php

use Clickatell\Clickatell;

$clickatell = new Clickatell('[username]', '[password]', [api_id], Clickatell::HTTP_API);

$clickatell->on('request', function($data) {
	// $data = The parameters passed to the request

    // The data array is passed by reference so you can change
    // any of the values before sending.

    // $data['message'] = "My Message Override."
    // $data['extra'] = array("mo" => true);
	print_r($data);
});

$clickatell->on('response', function($data) {
	// $data = The result of the API call.

	// This hook can be used to register multiple
	// listeners that can log to file/db or call another
	// service.
	print_r($data);
});

?>
```

5. Dealing with unsupported parameters
--------------------------------------

Some parameters are not supported by default, but can still be passed to the individual



6. Callbacks
---------------

You can listen to clickatell callbacks by using the `parseCallback();` function. It's a helper function
to make sure the required parameters are including in the `$_GET` array.

Parameters: apiMsgId, cliMsgId, to, timestamp, from, status, charge

Example

``` php
<?php

use Clickatell\Clickatell;


Clickatell::parseCallback(function ($values) {

    // var_dump($values);

});

?>
```