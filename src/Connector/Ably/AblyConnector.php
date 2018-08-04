<?php

namespace SP\RealTimeBundle\Connector\Ably;

use Ably\AblyRest;
use Ably\Exceptions\AblyException;
use SP\RealTimeBundle\Connector\ConnectorInterface;
use SP\RealTimeBundle\Connector\ConnectorTokenInterface;
use SP\RealTimeBundle\Message\Message;

class AblyConnector implements ConnectorInterface
{
    /**
     * @var AblyRest Ably REST Client used to publish messages and make tokens
     */
    private $ablyClient;

    /**
     * @var int Token TTL in seconds
     */
    private $ttl;

    /**
     * RealtimeHelper constructor.
     *
     * @param string $ablyApiKey API key used by Ably REST client
     * @param int TTL for each token requested
     */
    public function __construct(string $ablyApiKey, int $ttl)
    {
        $this->ablyClient = new AblyRest(['key' => $ablyApiKey]);
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AblyException if the provided arguments are invalid or the request fails
     */
    public function broadcast(string $channel, Message $message)
    {
        $this->ablyClient->channel($channel)->publish($channel, $message->jsonSerialize());
    }

    /**
     * {@inheritdoc}
     *
     * @return ConnectorTokenInterface
     */
    public function subscribe(string $channel): ConnectorTokenInterface
    {
        return new AblyToken(
            $this->ablyClient->auth->requestToken([
                'capability' => [
                    $channel => ['subscribe'],
                ],
                'ttl' => $this->ttl * 1000,
            ])
        );
    }
}
