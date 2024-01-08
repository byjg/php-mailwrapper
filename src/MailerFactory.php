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
    private static array $config = [];

    /**
     * @param string $class
     * @throws InvalidMailHandlerException
     */
    public static function registerMailer(string $class)
    {
        if (!in_array(MailWrapperInterface::class, class_implements($class))) {
            throw new InvalidMailHandlerException('Class not implements ConnectorInterface!');
        }

        /** @var MailWrapperInterface $class */
        $protocolList = $class::schema();
        foreach ((array)$protocolList as $item) {
            self::$config[$item] = $class;
        }
    }


    /**
     * @param string $connection
     * @return MailWrapperInterface
     * @throws ProtocolNotRegisteredException
     */
    public static function create(string $connection): MailWrapperInterface
    {
        $uri = new Uri($connection);

        if (!isset(self::$config[$uri->getScheme()])) {
            throw new ProtocolNotRegisteredException('Protocol not found/registered!');
        }

        $class = self::$config[$uri->getScheme()];

        return new $class($uri);
    }
}
