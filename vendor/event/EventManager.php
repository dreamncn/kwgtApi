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
     * 自动注册事件
     */
    public static function register(){
        $data = scandir(APP_EXTEND);
        foreach ($data as $value) {
            if ($value != '.' && $value != '..') {
                $file = APP_EXTEND . DS . $value . DS . 'register.php';
                if (file_exists($file)) include $file;
            }
        }
    }
    /**
     * 绑定事件
     * @param String $eventName
     * @param String $listener
     */
    public static function attach(String $eventName, String $listener)
    {
        //一个事件名绑定多个监听器
        self::$eventList[$eventName][] = $listener;
    }

    /**
     * 解除绑定事件
     * @param String $eventName
     */
    public static function detach($eventName)
    {
        unset(self::$eventList[$eventName]);
    }

    /**
     * 触发事件
     * @param String $eventName
     * @param $data
     */
    public static function fire(string $eventName, $data)
    {
        foreach (self::$eventList as $attachEventName => $listenerList) {
            //匹配监听列表
            if ($eventName == $attachEventName) {
                foreach ($listenerList as $eventListener) {
                    (new $eventListener())->handleEvent($data);
                }
            }
        }
    }
}

