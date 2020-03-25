<?php

namespace SP\RealTimeBundle\Event;

use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

abstract class AbstractEvent extends BaseEvent
{
    abstract public function getEventName(): string;
}
