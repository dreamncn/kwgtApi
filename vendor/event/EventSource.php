<?php


namespace app\vendor\event;


class EventSource
{
    protected $eventName;
    protected $eventData;

    public function getEventName()
    {
        return $this->eventName;
    }

    public function getEventData()
    {
        return $this->eventData;
    }
}
