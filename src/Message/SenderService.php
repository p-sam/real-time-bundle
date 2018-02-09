<?php

namespace SP\RealTimeBundle\Message;

use SP\RealTimeBundle\Event\EventDispatcher;
use SP\RealTimeBundle\Event\MessageEvent;
use SP\RealTimeBundle\Presence\PresenceService;
use SP\RealTimeBundle\RealTimeConfiguration;

class SenderService
{
    /**
     * @var RealTimeConfiguration
     */
    private $configuration;

    /**
     * @var PresenceService
     */
    private $presenceService;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(RealTimeConfiguration $configuration, EventDispatcher $eventDispatcher, PresenceService $presenceService)
    {
        $this->configuration = $configuration;
        $this->presenceService = $presenceService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Broadcasts a message to the specified channel.
     *
     * @param string $channel
     * @param string $message
     */
    public function broadcast(string $channel, string $message)
    {
        if ($this->presenceService->exists($channel)) {
            $this->broadcastWithoutCheckingPresence($channel, $message);
        }
    }

    /**
     * Broadcasts a message to the specified channel even if no client seems subscribed.
     *
     * @param string $channel
     * @param string $message
     */
    public function broadcastWithoutCheckingPresence(string $channel, string $message)
    {
        $this->configuration->getConfiguredConnector()->broadcast($channel, $message);

        $this->eventDispatcher->dispatch(new MessageEvent($channel, $message));
    }
}
