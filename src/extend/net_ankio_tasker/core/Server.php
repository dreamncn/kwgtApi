<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\net_ankio_tasker\core;


class Server
{
    private static $handler;
    /**
     * +----------------------------------------------------------
     * 启动任务扫描服务
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    public function start(){
        try{
            self::$handler=fopen(EXTEND_TASKER.DS."data".DS."tasker_server.lock", 'a');
            flock(self::$handler, LOCK_EX);
            //第一次运行，锁定后进行操作
            $this->scan();
        }catch (\Exception $e){

        }
    }

    public function stop(){
        try{
            flock(self::$handler, LOCK_UN);
            fclose(self::$handler);
            unlink(EXTEND_TASKER.DS."data".DS."tasker_server.lock");
        }catch (\Exception $e){

        }
    }

    public function sync(){

    }

    public function scan(){
        do {
           //循环扫描
        } while(true);
    }
}