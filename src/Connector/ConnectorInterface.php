<?php

namespace SP\RealTimeBundle\Connector;

interface ConnectorInterface
{
    /**
     * Broadcasts a message to the specified channel.
     *
     * @param string $channel
     * @param string $message
     */
    public function broadcast(string $channel, string $message);

    /**
     * @param string $channel
     *
     * @return ConnectorTokenInterface
     */
    public function subscribe(string $channel): ConnectorTokenInterface;
}
