<?php

namespace ByJG\Mail;

class SendResult
{
    public function __construct(
        public bool $success,
        public ?string $id = null,
    ) {}
}