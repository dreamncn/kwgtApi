<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\vendor\event;


/**
 * Interface EventListener
 * @package app\vendor\event
 */
interface EventListener
{

    /**
     * +----------------------------------------------------------
     * 事件接收器
     * +----------------------------------------------------------
     * @param $event string 事件名
     * @param $msg string|array 自行判断类型
     * +----------------------------------------------------------
     * @return mixed
     * +----------------------------------------------------------
     */
	public function handleEvent($event,$msg);
}
