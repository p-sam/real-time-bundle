<?php

namespace SP\RealTimeBundle\Connector\Ably;

use Ably\Models\TokenDetails;
use SP\RealTimeBundle\Connector\ConnectorTokenInterface;

class AblyToken implements ConnectorTokenInterface
{
    /**
     * @var TokenDetails Wrapped Ably Token
     */
    private $tokenDetails;

    /**
     * AblyToken constructor.
     *
     * @param TokenDetails $tokenDetails
     */
    public function __construct(TokenDetails $tokenDetails)
    {
        $this->tokenDetails = $tokenDetails;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'ably';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return $this->tokenDetails->clientId;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDate(): \DateTime
    {
        return \DateTime::createFromFormat('U', (int) ($this->tokenDetails->expires / 1000));
    }

    /**
     * {@inheritdoc}
     */
    public function getTtl(): int
    {
        return (int) (($this->tokenDetails->expires - $this->tokenDetails->issued) / 1000);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->tokenDetails->toArray();
    }
}
