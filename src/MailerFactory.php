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
    /** @var array<string, class-string<MailWrapperInterface>> */
    private static array $config = [];

    /**
     * @param class-string<MailWrapperInterface> $class
     *
     * @throws InvalidMailHandlerException
     *
     * @return void
     */
    public static function registerMailer(string $class): void
    {
        $implements = class_implements($class);
        if ($implements === false || !in_array(MailWrapperInterface::class, $implements)) {
            throw new InvalidMailHandlerException('Class not implements ConnectorInterface!');
        }

        /** @var class-string<MailWrapperInterface> $class */
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
        $uri = is_string($connection) ? new Uri($connection) : $connection;

        $scheme = $uri->getScheme();
        if (!isset(self::$config[$scheme])) {
            throw new ProtocolNotRegisteredException('Protocol not found/registered!');
        }

        $class = self::$config[$scheme];

        return new $class($uri);
    }
}
