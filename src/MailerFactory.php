<?php
/**
 * User: jg
 * Date: 28/05/17
 * Time: 11:50
 */

namespace ByJG\Mail;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Util\Uri;

class MailerFactory
{
    private static $config = [];

    /**
     * @param string $protocol
     * @param string $class
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     */
    public static function registerMailer($protocol, $class)
    {
        if (!class_exists($class, true)) {
            throw new InvalidMailHandlerException('Class not found!');
        }
        self::$config[$protocol] = $class;
    }

    /**
     * @param $connection
     * @return mixed
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
