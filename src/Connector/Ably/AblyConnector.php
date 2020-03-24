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
     * @var int TTL to send to the client
     */
    private $clientTtl;

    /**
     * RealtimeHelper constructor.
     *
     * @param string   $ablyApiKey     API key used by Ably REST client
     * @param int      $ttl            TTL for each token requested
     * @param null|int $forceClientTtl TTL to be returned to the client
     */
    public function __construct(string $ablyApiKey, int $ttl, ?int $forceClientTtl = null)
    {
        $this->ablyClient = new AblyRest(['key' => $ablyApiKey]);
        $this->ttl = $ttl;
        if (null === $forceClientTtl) {
            $this->clientTtl = $ttl;
        } elseif ($forceClientTtl > $ttl) {
            throw new \InvalidArgumentException('$forceClientTtl > $ttl');
        } else {
            $this->clientTtl = $forceClientTtl;
        }
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
            ]),
            $this->clientTtl
        );
    }
}
