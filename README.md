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

Clickatell has a couple of different API's that each support a subset of functions. We are going to refer to them as
Messaging and Bulk Messaging API's for this document.

### Messaging API's

The following are all messaging API's.

` Clickatell\Component\Transport\TransportHttp `

` Clickatell\Component\Transport\TransportSoap `

` Clickatell\Component\Transport\TransportXml `

` Clickatell\Component\Transport\TransportSmtp `

These Transports all support the following functions

`sendMessage(array $to, string $message, $from = "", $callback = true);`

`getBalance();`

`queryMessage($apiMsgId);`

`routeCoverage($msisdn);`

`getMessageCharge($apiMsgId);`

### Bulk Messaging API's

The following are bulk messaging API's. The have only a limited number of functions and are more suited for bulk messaging. Since they aren't processed in real time, these Transports do not
return the same results as the normal messaging API's.

` Clickatell\Component\Transport\TransportSMTP `

These Transports all support the following functions

`sendMessage(array $to, string $message, $from = "", $callback = true);`