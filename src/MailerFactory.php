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
use Psr\Http\Message\UriInterface;

class MailerFactory
{
    private static array $config = [];

    /**
     * @param string $class
     *
     * @throws InvalidMailHandlerException
     *
     * @return void
     */
    public static function registerMailer(string $class): void
    {
        if (!in_array(MailWrapperInterface::class, class_implements($class))) {
            throw new InvalidMailHandlerException('Class not implements ConnectorInterface!');
        }

        /** @var MailWrapperInterface $class */
        $protocolList = $class::schema();
        foreach ($protocolList as $item) {
            self::$config[$item] = $class;
        }
    }


    /**
     * @param Uri|string $connection
     * @return MailWrapperInterface
     * @throws ProtocolNotRegisteredException
     */
    public static function create(UriInterface|string $connection): MailWrapperInterface
    {
        $uri = $connection;
        if (is_string($connection)) {
            $uri = new Uri($connection);
        }

        if (!isset(self::$config[$uri->getScheme()])) {
            throw new ProtocolNotRegisteredException('Protocol not found/registered!');
        }

        $class = self::$config[$uri->getScheme()];

        return new $class($uri);
    }
}
