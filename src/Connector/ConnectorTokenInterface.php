<?php

namespace SP\RealTimeBundle\Connector;

interface ConnectorTokenInterface extends \JsonSerializable
{
    /**
     * @return string The token type
     */
    public function getType(): string;

    /**
     * @return string The identifier for this session
     */
    public function getIdentifier(): string;

    /**
     * @return \DateTime The token expiration date
     */
    public function getExpirationDate(): \DateTime;

    /**
     * @return int seconds between when the token was issued and when it expires
     */
    public function getTtl(): int;
}
