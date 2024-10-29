<?php

namespace ByJG\Mail;

use PHPMailer\PHPMailer\PHPMailer;

class Util
{

    /**
     * Get Full Email Name
     *
     * @param String $email
     * @param string|null $name
     * @return String
     */
    public static function getFullEmail(string $email, ?string $name = ""): string
    {
        if (!empty($name)) {
            return "\"" . $name . "\" <" . $email . ">";
        } else {
            return $email;
        }
    }

    public static function decomposeEmail(string $fullEmail): array
    {
        $pat = "/[\"'](?P<name>[\S\s]*)[\"']\s+<(?P<email>.*)>/";
        $pat2 = "/<(?P<email>.*)>/";

        $email = $fullEmail;
        $name = "";

        if (preg_match($pat, $fullEmail, $parts)) {
            if (array_key_exists("name", $parts)) {
                $name = $parts["name"];
            }

            if (array_key_exists("email", $parts)) {
                $email = $parts["email"];
            }
            return array("email" => $email, "name" => $name);
        }

        if (preg_match($pat2, $fullEmail, $parts)) {
            if (array_key_exists("email", $parts)) {
                $email = $parts["email"];
            }
        }

        return array("email" => $email, "name" => $name);
    }

    public static function isValidEmail(string $email): bool
    {
        $ret = PHPMailer::validateAddress($email);
        return (is_numeric($ret) ? $ret == 1 : $ret);
    }
}
