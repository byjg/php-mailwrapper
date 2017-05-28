<?php

namespace Test;

use ByJG\Mail\Envelope;
use PHPUnit\Framework\TestCase;

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

abstract class BaseWrapperTest extends TestCase
{
    /**
     * @return \ByJG\Mail\Envelope
     */
    public function getBasicEnvelope()
    {
        $envelope = new Envelope('from@email.com', 'to@email.com', 'Subject', '<h1>Title</h1>Body');
        return $envelope;
    }

    protected function fixVariableFields($text)
    {
        $text = preg_replace(
            [
                '~\w+, \d+ \w+ \w+ \d+:\d+:\d+ \+\d+~',
                '~([_<])\w{32}([@"\n-])~',
                '<boundarydelimiter@\w>'
            ],
            [
                'xxx, dd, yyyy hh:mi:ss +ffff',
                '$1boundarydelimiter$2',
                '<boundarydelimiter@host>'
            ],
            $text
        );

        return $text;
    }

}
