<?php
/**
 * User: jg
 * Date: 28/05/17
 * Time: 11:50
 */

namespace ByJG\Mail;

use ByJG\Util\Uri;

class MailerFactory
{
    private static $config = [];

    public static function registerMailer($protocol, $class)
    {
        if (!class_exists($class, true)) {
            throw new \Exception('Class not found!');
        }
        self::$config[$protocol] = $class;
    }

    public static function create($connection)
    {
        $uri = new Uri($connection);

        if (!isset(self::$config[$uri->getScheme()])) {
            throw new \Exception('Protocol not found/registered!');
        }

        $class = self::$config[$uri->getScheme()];

        return new $class($uri);
    }
}
