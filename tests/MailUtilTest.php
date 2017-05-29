<?php

use ByJG\Mail\Util;

use PHPUnit\Framework\TestCase;

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

class MailUtilTest extends TestCase
{
    const EMAIL_OK = 'joao@server.com.br';
    const EMAIL_NOK_1 = 'joao@server.com.';
    const EMAIL_NOK_2 = 'joao@server@com';
    const EMAIL_NOK_3 = 'joao @ local-';
    const EMAIL_NOK_4 = 'joao@server(.111';
    const EMAIL_NOK_5 = 'joao-server.com';


    // Run before each test case
    function setUp()
    {
    }

    // Run end each test case
    function teardown()
    {
    }

    function test_IsValidEmail()
    {
        $this->assertTrue(Util::isValidEmail(self::EMAIL_OK));
        $this->assertTrue(!Util::isValidEmail(self::EMAIL_NOK_1));
        $this->assertTrue(!Util::isValidEmail(self::EMAIL_NOK_2));
        $this->assertTrue(!Util::isValidEmail(self::EMAIL_NOK_3));
        $this->assertTrue(!Util::isValidEmail(self::EMAIL_NOK_4));
        $this->assertTrue(!Util::isValidEmail(self::EMAIL_NOK_5));
    }

    function test_GetFullEmailName()
    {
        $this->assertEquals(Util::getFullEmail("joao@server.com.br", "Joao"), '"Joao" <joao@server.com.br>');
        $this->assertEquals(Util::getFullEmail("joao@server.com.br", ""), 'joao@server.com.br');
        $this->assertEquals(Util::getFullEmail("joao@server.com.br"), 'joao@server.com.br');
    }

    function test_GetEmailPair()
    {
        $pair = Util::decomposeEmail('"Name" <email@domain.com>');
        $this->assertEquals($pair["name"], 'Name');
        $this->assertEquals($pair["email"], 'email@domain.com');

        $pair = Util::decomposeEmail('"" <email@domain.com>');
        $this->assertEquals($pair["name"], '');
        $this->assertEquals($pair["email"], 'email@domain.com');

        $pair = Util::decomposeEmail('<email@domain.com>');
        $this->assertEquals($pair["name"], '');
        $this->assertEquals($pair["email"], 'email@domain.com');

        $pair = Util::decomposeEmail('email@domain.com');
        $this->assertEquals($pair["name"], '');
        $this->assertEquals($pair["email"], 'email@domain.com');

        $pair = Util::decomposeEmail('"João" <email@domain.com>');
        $this->assertEquals($pair["name"], '=?iso-8859-1?Q?Jo=E3o?=');
        $this->assertEquals($pair["email"], 'email@domain.com');

    }
}
