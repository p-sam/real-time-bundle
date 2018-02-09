<?php

namespace SP\RealTimeBundle\Event;

class MessageEvent extends AbstractEvent
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $message;

    /**
     * MessageEvent constructor.
     *
     * @param string $channel
     * @param string $message
     */
    public function __construct(string $channel, string $message)
    {
        $this->channel = $channel;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'message';
    }
}
