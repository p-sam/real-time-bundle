<?php

namespace SP\RealTimeBundle\Event;

use SP\RealTimeBundle\Presence\PresenceToken;

class SubscribeEvent extends AbstractEvent
{
    /**
     * @var PresenceToken
     */
    private $token;

    /**
     * AbstractTokenEvent constructor.
     *
     * @param PresenceToken $token
     */
    public function __construct(PresenceToken $token)
    {
        $this->token = $token;
    }

    /**
     * @return PresenceToken
     */
    public function getToken(): PresenceToken
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->token->getChannel();
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'subscribe';
    }
}
