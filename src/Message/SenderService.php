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
     * @param string        $channel
     * @param Message|mixed $dataOrMessage
     */
    public function broadcast(string $channel, $dataOrMessage)
    {
        if ($this->presenceService->exists($channel)) {
            $this->broadcastWithoutCheckingPresence($channel, $dataOrMessage);
        }
    }

    /**
     * Broadcasts a message to the specified channel even if no client seems subscribed.
     *
     * @param string        $channel
     * @param Message|mixed $message
     * @param mixed         $dataOrMessage
     */
    public function broadcastWithoutCheckingPresence(string $channel, $dataOrMessage)
    {
        if ($dataOrMessage instanceof Message) {
            $message = $dataOrMessage;
        } else {
            $message = new Message($dataOrMessage);
        }

        $this->configuration->getConfiguredConnector()->broadcast($channel, $message);

        $this->eventDispatcher->dispatch(new MessageEvent($channel, $message));
    }
}
