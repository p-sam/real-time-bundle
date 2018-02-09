<?php

namespace SP\RealTimeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    abstract public function getEventName(): string;
}
