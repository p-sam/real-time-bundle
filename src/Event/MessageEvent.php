<?php

namespace SP\RealTimeBundle\Event;

use SP\RealTimeBundle\Message\Message;

class MessageEvent extends AbstractEvent
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var Message
     */
    private $message;

    /**
     * MessageEvent constructor.
     *
     * @param string  $channel
     * @param Message $message
     */
    public function __construct(string $channel, Message $message)
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
     * @return Message
     */
    public function getMessage(): Message
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
