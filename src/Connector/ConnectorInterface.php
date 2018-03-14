<?php

namespace SP\RealTimeBundle\Connector;

use SP\RealTimeBundle\Message\Message;

interface ConnectorInterface
{
    /**
     * Broadcasts a message to the specified channel.
     *
     * @param string  $channel
     * @param Message $message
     */
    public function broadcast(string $channel, Message $message);

    /**
     * @param string $channel
     *
     * @return ConnectorTokenInterface
     */
    public function subscribe(string $channel): ConnectorTokenInterface;
}
