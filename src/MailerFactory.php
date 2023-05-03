<?php
/**
 * User: jg
 * Date: 28/05/17
 * Time: 11:50
 */

namespace ByJG\Mail;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\Wrapper\MailWrapperInterface;
use ByJG\Util\Uri;

class MailerFactory
{
    private static $config = [];

    /**
     * @param string $protocol
     * @param string $class
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     */
    public static function registerMailer($class)
    {
        if (!in_array(MailWrapperInterface::class, class_implements($class))) {
            throw new InvalidMailHandlerException('Class not implements ConnectorInterface!');
        }

        $protocolList = $class::schema();
        foreach ((array)$protocolList as $item) {
            self::$config[$item] = $class;
        }
    }


    /**
     * @param $connection
     * @return \ByJG\Mail\Wrapper\MailWrapperInterface
     * @throws \ByJG\Mail\Exception\ProtocolNotRegisteredException
     */
    public static function create($connection)
    {
        $uri = new Uri($connection);

        if (!isset(self::$config[$uri->getScheme()])) {
            throw new ProtocolNotRegisteredException('Protocol not found/registered!');
        }

        $class = self::$config[$uri->getScheme()];

        return new $class($uri);
    }
}
