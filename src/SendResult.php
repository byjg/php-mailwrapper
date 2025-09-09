<?php

namespace ByJG\Mail;

class SendResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $id = null,
    ) {}
}