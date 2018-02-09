<?php

namespace SP\RealTimeBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface as KernelEventDispatcher;

class EventDispatcher
{
    /**
     * @var KernelEventDispatcher
     */
    private $kernelEventDispatcher;

    public function __construct(KernelEventDispatcher $kernelEventDispatcher)
    {
        $this->kernelEventDispatcher = $kernelEventDispatcher;
    }

    public function dispatch(AbstractEvent $event)
    {
        $this->kernelEventDispatcher->dispatch('sp_real_time.events.'.$event->getEventName(), $event);
    }
}
