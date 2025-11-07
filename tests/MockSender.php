<?php

namespace Tests;

use Aws\Result;

class MockSender
{
    public string $result;

    // AmazonSes
    public function sendRawEmail(string $raw): Result
    {
        $this->result = $raw;

        return new Result([
            'MessageId' => 'EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000',
        ]);
    }

    // Mailgun Wrapper
    public function postMultiPartForm($message): string
    {
        $this->result = $message;
        return '{"id": "123445"}';
    }
}