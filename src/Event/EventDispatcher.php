<?php

namespace SP\RealTimeBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $kernelEventDispatcher;

    public function __construct(EventDispatcherInterface $kernelEventDispatcher)
    {
        $this->kernelEventDispatcher = $kernelEventDispatcher;
    }

    public function dispatch(AbstractEvent $event)
    {
        $this->kernelEventDispatcher->dispatch($event, 'sp_real_time.events.'.$event->getEventName());
    }
}
