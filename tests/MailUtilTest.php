<?php

namespace Tests;

use ByJG\Mail\Util;

use PHPUnit\Framework\TestCase;

class MailUtilTest extends TestCase
{
    const EMAIL_OK = 'joao@server.com.br';
    const EMAIL_NOK_1 = 'joao@server.com.';
    const EMAIL_NOK_2 = 'joao@server@com';
    const EMAIL_NOK_3 = 'joao @ local-';
    const EMAIL_NOK_4 = 'joao@server(.111';
    const EMAIL_NOK_5 = 'joao-server.com';

    function test_IsValidEmail(): void
    {
        $this->assertTrue(Util::isValidEmail(self::EMAIL_OK));
        $this->assertFalse(Util::isValidEmail(self::EMAIL_NOK_1));
        $this->assertFalse(Util::isValidEmail(self::EMAIL_NOK_2));
        $this->assertFalse(Util::isValidEmail(self::EMAIL_NOK_3));
        $this->assertFalse(Util::isValidEmail(self::EMAIL_NOK_4));
        $this->assertFalse(Util::isValidEmail(self::EMAIL_NOK_5));
    }

    function test_GetFullEmailName(): void
    {
        $this->assertEquals(Util::getFullEmail("joao@server.com.br", "Joao"), '"Joao" <joao@server.com.br>');
        $this->assertEquals(Util::getFullEmail("joao@server.com.br", ""), 'joao@server.com.br');
        $this->assertEquals(Util::getFullEmail("joao@server.com.br"), 'joao@server.com.br');
    }

    function test_GetEmailPair(): void
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
        $this->assertEquals($pair["name"], 'João');
        $this->assertEquals($pair["email"], 'email@domain.com');

    }
}
