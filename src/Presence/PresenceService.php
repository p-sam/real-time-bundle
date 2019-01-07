<?php

namespace SP\RealTimeBundle\Presence;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SP\RealTimeBundle\Event\EventDispatcher;
use SP\RealTimeBundle\Event\SubscribeEvent;
use SP\RealTimeBundle\RealTimeConfiguration;

class PresenceService
{
    /**
     * @var RealTimeConfiguration
     */
    private $configuration;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * PresenceService constructor.
     *
     * @param RealTimeConfiguration $configuration
     * @param EventDispatcher       $eventDispatcher
     */
    public function __construct(RealTimeConfiguration $configuration, EventDispatcher $eventDispatcher)
    {
        $this->configuration = $configuration;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $channel
     *
     * @return PresenceToken
     */
    public function subscribe(string $channel): PresenceToken
    {
        $connectorToken = $this->configuration->getConfiguredConnector()->subscribe($channel);
        $token = new PresenceToken($channel, $connectorToken, Uuid::uuid4());

        $this->configuration->getConfiguredPresenceStorage()->store($token);

        $this->eventDispatcher->dispatch(new SubscribeEvent($token));

        return $token;
    }

    /**
     * @param string        $channel
     * @param UuidInterface $uuid
     *
     * @return bool true if a PresenceToken was actually removed
     */
    public function unsubscribe(string $channel, UuidInterface $uuid): bool
    {
        return $this->configuration->getConfiguredPresenceStorage()->remove($channel, $uuid);
    }

    /**
     * @param string $channel
     * @param bool   $forceCheck
     *
     * @return bool true if the channel contains at least one presence
     */
    public function exists(string $channel, bool $forceCheck = false): bool
    {
        if (!$forceCheck && !$this->configuration->isPresenceCheckingEnabled()) {
            return true;
        }

        return $this->configuration->getConfiguredPresenceStorage()->channelExists($channel);
    }

    /**
     * ensure every/specified channel knows its last presence.
     *
     * @param null|string $channel null for all
     */
    public function sync(?string $channel)
    {
        $this->configuration->getConfiguredPresenceStorage()->syncChannelLast($channel);
    }
}
