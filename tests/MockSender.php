<?php

namespace Test;

class MockSender
{
    public $result;

    public function sendRawEmail($raw)
    {
        $this->result = $raw;
    }
}