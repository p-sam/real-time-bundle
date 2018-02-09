<?php

namespace SP\RealTimeBundle\Presence;

use Ramsey\Uuid\UuidInterface;
use SP\RealTimeBundle\Connector\ConnectorTokenInterface;

class PresenceToken implements \JsonSerializable
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var ConnectorTokenInterface
     */
    private $token;

    /**
     * @var UuidInterface
     */
    private $uuid;

    public function __construct(string $channel, ConnectorTokenInterface $token, UuidInterface $uuid)
    {
        $this->channel = $channel;
        $this->token = $token;
        $this->uuid = $uuid;
    }

    /**
     * @return ConnectorTokenInterface
     */
    public function getToken(): ConnectorTokenInterface
    {
        return $this->token;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return array The json response to a subscription
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->uuid->toString(),
            'type' => $this->token->getType(),
            'expires' => $this->token->getExpirationDate()->format('c'),
            'ttl' => $this->token->getTtl(),
            'payload' => $this->token->jsonSerialize(),
        ];
    }
}
