<?php


namespace app\vendor\event;


interface EventListener
{
    //事件接收器
    public function handleEvent($msg);
}
