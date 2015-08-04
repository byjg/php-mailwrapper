<?php

namespace ByJG\Mail;

use ByJG\Convert\FromUTF8;
use PHPMailer;

class Util
{

	/**
	 * Get Full Email Name
	 *
	 * @param String $name
	 * @param String $email
	 * @return String
	 */
	public static function getFullEmail($email, $name = "")
	{
		if (!empty($name)) {
            return "\"" . $name . "\" <" . $email . ">";
        } else {
            return $email;
        }
    }

	public static function decomposeEmail($fullEmail)
	{
		$pat = "/[\"'](?P<name>[\S\s]*)[\"']\s+<(?P<email>.*)>/";
		$pat2 = "/<(?P<email>.*)>/";

		$email = $fullEmail;
		$name = "";

        $parts = null;
		if (preg_match ( $pat, $fullEmail, $parts ))
		{
			if (array_key_exists("name", $parts)) {
                $name = FromUTF8::toIso88591Email($parts["name"]);
            }

            if (array_key_exists("email", $parts)) {
                $email = $parts["email"];
            }
        }
		else if (preg_match($pat2, $fullEmail, $parts))
		{
			if (array_key_exists("email", $parts)) {
                $email = $parts["email"];
            }
        }

		return array("email"=>$email, "name"=>$name);
	}

	public static function isValidEmail($email)
	{
		$ret = PHPMailer::ValidateAddress($email);
		return (is_numeric($ret) ? $ret == 1 : $ret);
	}
}

