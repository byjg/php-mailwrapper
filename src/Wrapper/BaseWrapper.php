<?php
/**
 * User: jg
 * Date: 28/05/17
 * Time: 12:31
 */

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Util\Uri;

abstract class BaseWrapper implements MailWrapperInterface
{
    /**
     * @var \ByJG\Util\Uri
     */
    protected $uri = null;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param \ByJG\Mail\Envelope $envelope
     * @throws \Exception
     */
    public function validate(Envelope $envelope)
    {
        if (0 === count($envelope->getTo())) {
            throw new \Exception("Destination Email was not provided");
        }

        if ($envelope->getFrom() == "") {
            throw new \Exception("From email was not provided");
        }
    }
}
