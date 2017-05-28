<?php

namespace Test;

class MockSender
{
    public $result;

    // AmazonSes
    public function sendRawEmail($raw)
    {
        $this->result = $raw;
    }

    // Mailgun Wrapper
    public function postMultiPartForm($message)
    {
        $this->result = $message;
        return '{"id": "123445"}';
    }
}