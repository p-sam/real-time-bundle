<?php

namespace SP\RealTimeBundle\Presence;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SP\RealTimeBundle\Connector\ConnectorInterface;
use SP\RealTimeBundle\Event\EventDispatcher;
use SP\RealTimeBundle\Event\SubscribeEvent;
use SP\RealTimeBundle\RealTimeConfiguration;

class PresenceService
{
    /**
     * @var ConnectorInterface
     */
    private $connector;

    /**
     * @var PresenceStorage
     */
    private $storage;

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
        $this->connector = $configuration->getConfiguredConnector();
        $this->storage = $configuration->getConfiguredPresenceStorage();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $channel
     *
     * @return PresenceToken
     */
    public function subscribe(string $channel): PresenceToken
    {
        $connectorToken = $this->connector->subscribe($channel);
        $token = new PresenceToken($channel, $connectorToken, Uuid::uuid4());

        $this->storage->store($token);

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
        return $this->storage->remove($channel, $uuid);
    }

    /**
     * @param string $channel
     *
     * @return bool true if the channel contains at least one presence
     */
    public function exists(string $channel): bool
    {
        return $this->storage->channelExists($channel);
    }
}
