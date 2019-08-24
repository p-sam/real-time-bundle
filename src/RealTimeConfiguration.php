<?php

namespace SP\RealTimeBundle;

use SP\RealTimeBundle\Connector\Ably\AblyConnector;
use SP\RealTimeBundle\Connector\ConnectorInterface;
use SP\RealTimeBundle\Presence\PresenceStorage;

class RealTimeConfiguration
{
    /**
     * @var ConnectorInterface The connector instantiated from configuration
     */
    private $connector;

    /**
     * @var PresenceStorage The presence persistence manager
     */
    private $presenceStorage;

    /**
     * @var bool Whether to check presence before sending
     */
    private $presenceCheckEnabled;

    /**
     * RealTimeConfiguration constructor.
     *
     * @param \Predis\Client $redis
     * @param array          $config
     *
     * @throws \Exception if resolving the redis client fails or throws an exception
     */
    public function __construct(\Predis\Client $redis, array $config)
    {
        // connector routing would happen here if multiple connectors were supported
        $this->connector = new AblyConnector($config['ably']['api_key'], $config['ably']['ttl'], $config['ably']['force_client_ttl']);

        $this->presenceStorage = new PresenceStorage(
            $redis,
            $config['redis']['key_prefix']
        );

        $this->presenceCheckEnabled = $config['presence_check'];
    }

    /**
     * @return ConnectorInterface
     */
    public function getConfiguredConnector(): ConnectorInterface
    {
        return $this->connector;
    }

    /**
     * @return PresenceStorage
     */
    public function getConfiguredPresenceStorage(): PresenceStorage
    {
        return $this->presenceStorage;
    }

    /**
     * @return bool
     */
    public function isPresenceCheckingEnabled(): bool
    {
        return $this->presenceCheckEnabled;
    }
}
