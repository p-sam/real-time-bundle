<?php

namespace SP\RealTimeBundle;

use SP\RealTimeBundle\Connector\Ably\AblyConnector;
use SP\RealTimeBundle\Connector\ConnectorInterface;
use SP\RealTimeBundle\Presence\PresenceStorage;
use Symfony\Component\DependencyInjection\Container;

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
     * RealTimeConfiguration constructor.
     *
     * @param Container $container
     * @param array     $config
     *
     * @throws \Exception if resolving the redis client fails or throws an exception
     */
    public function __construct(Container $container, array $config)
    {
        // connector routing would happen here if multiple connectors were supported
        $this->connector = new AblyConnector($config['ably']['api_key'], $config['ably']['ttl']);

        $this->presenceStorage = new PresenceStorage(
            $container->get($config['redis']['client']),
            $config['redis']['key_prefix']
        );
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
}
