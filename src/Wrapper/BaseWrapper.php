<?php
/**
 * User: jg
 * Date: 28/05/17
 * Time: 12:31
 */

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Util\Uri;

abstract class BaseWrapper implements MailWrapperInterface
{
    /**
     * @var Uri
     */
    protected ?Uri $uri = null;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param Envelope $envelope
     * @throws InvalidEMailException
     */
    public function validate(Envelope $envelope): void
    {
        if (0 === count($envelope->getTo())) {
            throw new InvalidEMailException("Destination Email was not provided");
        }

        if ($envelope->getFrom() == "") {
            throw new InvalidEMailException("From email was not provided");
        }
    }
}
