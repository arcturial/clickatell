<?php
namespace Clickatell;

use Clickatell\Component\Request as Request;
use Clickatell\Component\Action as Action;
use Clickatell\Component\Transfer\TransferCurl as TransferCurl;
use Clickatell\Component\Translate\TranslateJson as TranslateJson;
use Clickatell\Component\Translate\TranslateInterface as TranslateInterface;
use Clickatell\Component\Transport\TransportInterface as TransportInterface;
use Clickatell\Exception\TransportException as TransportException;

/**
 * This is the dependency container. It works like a factory in producing certain
 * objects that other classes depends on. This class can be modifed to change the
 * default objects used.
 *
 * @package Clickatell
 * @author Chris Brand
 */
class ClickatellContainer
{
    /**
     * Creates an action handler. The action handler groups requests to the Transport and Translate
     * interfaces to generate the correct response.
     *
     * @param Clickatell\Component\Transport\TransportInterface $transport
     * @param Clickatell\Component\Translate\TranslateInterface $translate
     * @return Clickatell\Component\Action
     */
    public static function createAction(TransportInterface $transport, TranslateInterface $translate)
    {
        $action = new Action($transport, $translate);

        return $action;
    }

    /**
     * Creates a request object with the uesrname\password\apiID as default parameters.
     *
     * @param string $username
     * @param string $password
     * @param int $apiId
     * @return Clickatell\Component\Request
     */
    public static function createRequest($username, $password, $apiId)
    {
        $request = new Request($username, $password, $apiId);

        return $request;
    }

    /**
     * Creates a Transport request. The Transport expects a Request object
     * in order to pull information on how to complete the call.
     *
     * @param string $transport
     * @param Clickatell\Component\Request $request
     * @return Clickatell\Component\Transport\TransportInterface
     * @throws TransportException
     */
    public static function createTransport($transport, Request $request)
    {
        if (class_exists($transport))
        {
            $object = new $transport(static::createTransfer(), $request);

            if ($object instanceof TransportInterface)
            {
                return $object;
            }
            else
            {
                throw new TransportException(TransportException::ERR_UNSUPPORTED_TRANSPORT);
            }
        }
        else
        {
            throw new TransportException(TransportException::ERR_TRANSPORT_NOT_FOUND);
        }
    }

    /**
     * Creates the Transfer handler. This function defines
     * the default Transfer protocol.
     *
     * @return Clickatell\Component\Transfer\TransferInterface
     */
    public static function createTransfer()
    {
        return new TransferCurl();
    }

    /**
     * Creates the Translate handler. This function defines
     * the default Translate handler.
     *
     * @return Clickatell\Component\Translate\TranslateInterface
     */
    public static function createTranslate()
    {
        return new TranslateJson;
    }
}