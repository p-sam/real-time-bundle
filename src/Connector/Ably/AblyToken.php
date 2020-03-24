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
     * @var int TTL to send to the client
     */
    private $clientTtl;

    /**
     * AblyToken constructor.
     *
     * @param TokenDetails $tokenDetails
     * @param int          $clientTtl
     */
    public function __construct(TokenDetails $tokenDetails, int $clientTtl)
    {
        $this->tokenDetails = $tokenDetails;
        $this->clientTtl = $clientTtl;
        if ($this->getTtl() < $clientTtl) {
            throw new \RuntimeException("AblyToken was created with clientTtl (${clientTtl}) bigger than the actual ttl: ".json_encode($this->getTtl()));
        }
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
        $tokenDetailsArray = $this->tokenDetails->toArray();
        // we force a client TTL to permit early token refresh
        $tokenDetailsArray['expires'] = $tokenDetailsArray['issued'] + ($this->clientTtl * 1000);

        return $tokenDetailsArray;
    }
}
