<?php

namespace SP\RealTimeBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event as ContractsBaseEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

if (is_subclass_of(EventDispatcher::class, EventDispatcherInterface::class)) {
    class Event extends ContractsBaseEvent
    {
    }
} else {
    class Event extends BaseEvent
    {
    }
}

abstract class AbstractEvent extends Event
{
    abstract public function getEventName(): string;
}
