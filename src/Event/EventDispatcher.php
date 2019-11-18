<?php

namespace SP\RealTimeBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface as KernelEventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

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
        if ($this->kernelEventDispatcher instanceof ContractsEventDispatcherInterface) {
            $this->kernelEventDispatcher->dispatch($event, 'sp_real_time.events.'.$event->getEventName());
        } else {
            $this->kernelEventDispatcher->dispatch('sp_real_time.events.'.$event->getEventName(), $event);
        }
    }
}
