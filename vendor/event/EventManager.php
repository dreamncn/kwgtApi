<?php
/**
 * 事件管理器
 * Class EventManager
 */

namespace app\vendor\event;
class EventManager
{
    private static $eventList = [];

    /**
     * 绑定事件
     * @param EventSource $eventObject
     * @param EventListener $listener
     */
    public static function attach(EventSource $eventObject, EventListener $listener)
    {
        //一个事件名绑定多个监听器
        self::$eventList[$eventObject->getEventName()][] = $listener;
    }

    /**
     * 解除绑定事件
     * @param EventSource $eventObject
     */
    public static function detach(EventSource $eventObject)
    {
        unset(self::$eventList[$eventObject->getEventName()]);
    }

    /**
     * 触发事件
     * @param EventSource $eventObject
     */
    public static function fire(EventSource $eventObject)
    {
        foreach (self::$eventList as $attachEventName => $listenerList) {
            //匹配监听列表
            if ($eventObject->getEventName() == $attachEventName) {
                foreach ($listenerList as $eventListener) {
                    $eventListener->handleEvent($eventObject->getEventData());
                }
            }
        }
    }
}

