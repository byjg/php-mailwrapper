<?php

namespace ByJG\Mail;

use InvalidArgumentException;

class MailConnection
{
    private $connParts = [];

    /**
     *   [0] => --IGNORE--
     *   [1] => TIPO: smtp / ssl
     *   [2] => USERNAME
     *   [3] => PASSWORD
     *   [4] => SERVER
     *   [5] => PORT
     *
     *   smtp://[USERNAME:PASSWORD@]SERVER[:PORT]
     *
     * @var array
     */
    public function __construct($smtpString)
    {
        if (($smtpString == "localhost") || ($smtpString == "")) {
            $smtpString = "smtp://localhost";
        }

        $pat = "/^(?P<protocol>\w+):\/\/(?:(?P<user>\S+):(?P<pass>\S+)@)?(?:(?P<server>[\w\d\-]+(?:\.[\w\d\-]+)*))(?::(?P<port>[\d]+))?$/";
        $match = preg_match($pat, $smtpString, $this->connParts);

        if (!$match || !isset($this->connParts["server"])) {
            throw new InvalidArgumentException("Wrong SMTP server definition. Expected at least a server and the protocols smtp, ssl, tls, ses ou mandrill.");
        }
    }

    public function getProtocol()
    {
        if (isset($this->connParts['protocol'])) {
            return $this->connParts['protocol'];
        } else {
            return false;
        }
    }

    public function getUsername()
    {
        if (isset($this->connParts['user'])) {
            return $this->connParts['user'];
        } else {
            return false;
        }
    }

    public function getPassword()
    {
        if (isset($this->connParts['pass'])) {
            return $this->connParts['pass'];
        } else {
            return false;
        }
    }

    public function getServer()
    {
        if (isset($this->connParts['server'])) {
            return $this->connParts['server'];
        } else {
            return false;
        }
    }

    public function getPort()
    {
        if (isset($this->connParts['port'])) {
            return $this->connParts['port'];
        } else {
            return 25;
        }
    }
}
