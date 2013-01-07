Clickatell SMS Messenger Library
================================

Master: [![Build Status](https://secure.travis-ci.org/arcturial/clickatell.png?branch=master)](http://travis-ci.org/arcturial/clickatell)

Development: [![Build Status](https://secure.travis-ci.org/arcturial/clickatell.png?branch=dev)](http://travis-ci.org/arcturial/clickatell)

This library allows easy access to connecting the Clickatell's Messenging API's.

### Table of Contents
* Installation
* Usage
* Supported API calls


1. Installation
------------------

Download the library to your application. The library has it's own autoloader so you can get it up and running by including on the main `Clickatell.php` file.

`require_once 'path/to/module/Clickatell.php'`


2. Usage
------------------

The Clickatell library allows you specify several ways to connect to Clickatell. The current ones supported are HTTP and XML. These connections are called "Transports".

The default transport is HTTP.

`$clickatell = new Clickatell($username, $password, $apiID);`

`$clickatell->sendMessage(1111111111, "My Message");`

You can specify a different output using the Clickatell constructor or using the setTransport() method.

`$clickatell = new Clickatell($username, $password, $apiID, Clickatell::TRANSPORT_XML);`

OR

`$clickatell = new Clickatell($username, $password, $apiID);`

`$clickatell->setTransport(new Clickatell\Component\Transport\TransportXml);`

NOTE: The library uses name spaces, and the Clickatell messenger is located at `Clickatell\Clickatell`

3. Supported API calls
------------------

The library currently supports the HTTP and XML API connections with the following methods available.

`sendMessage($to, $message, $from = "", $callback = true);`

`getBalance();`

`queryMessage($apiMsgId);`

`routeCoverage($msisdn);`

`getMessageCharge($apiMsgId);`