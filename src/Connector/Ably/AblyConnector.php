<?php

namespace SP\RealTimeBundle\Connector\Ably;

use Ably\AblyRest;
use Ably\Exceptions\AblyException;
use SP\RealTimeBundle\Connector\ConnectorInterface;
use SP\RealTimeBundle\Connector\ConnectorTokenInterface;

class AblyConnector implements ConnectorInterface
{
    /**
     * @var AblyRest Ably REST Client used to publish messages and make tokens
     */
    private $ablyClient;

    /**
     * RealtimeHelper constructor.
     *
     * @param string $ablyApiKey API key used by Ably REST client
     */
    public function __construct(string $ablyApiKey)
    {
        $this->ablyClient = new AblyRest(['key' => $ablyApiKey]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws AblyException if the provided arguments are invalid or the request fails
     */
    public function broadcast(string $channel, string $message)
    {
        $this->ablyClient->channel($channel)->publish($channel, $message);
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
            ])
        );
    }
}
