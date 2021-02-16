<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\net_ankio_tasker\core;


use app\vendor\debug\Log;
use app\vendor\web\Response;

/**
 * +----------------------------------------------------------
 * Class Server
 * +----------------------------------------------------------
 * @package app\extend\net_ankio_tasker\core
 * +----------------------------------------------------------
 * Date: 2020/12/31 09:57
 * Author: ankio
 * +----------------------------------------------------------
 * Desciption:Tasker服务
 * +----------------------------------------------------------
 */
class Server
{

    private static $instance=null;

    private $taskerUrl;

    public function __construct()
    {
        Db::initLock();
        $this->taskerUrl=Response::getAddress()."/tasker_server/";
        //任务URL
    }

    /**
     * +----------------------------------------------------------
     * 获取对象实例
     * +----------------------------------------------------------
     * @return Server
     * +----------------------------------------------------------
     */
    public static function getInstance(){
        return self::$instance===null?(self::$instance=new Server()):self::$instance;
    }

    /**
     * +----------------------------------------------------------
     * 定时任务路由，用于对定时任务进行路由
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    public  function route()
    {
        $splite=explode("/",$_SERVER['REQUEST_URI']);

        if(sizeof($splite)!==3)return;
        if($splite[1]!=="tasker_server")return;

        Async::response(0);
        switch ($splite[2]){
            case "init":$this->init();break;
        }
    }

    /**
     * +----------------------------------------------------------
     * 启动任务扫描服务
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    public function start(){
        if(!$this->isLock()){//没有锁定，请求保持锁定
            $bool=Async::request($this->taskerUrl."init","GET",[],[],"tasker_start");
            Log::debug("Tasker","定时任务服务启动。");
        }
    }

    /**
     * +----------------------------------------------------------
     *  停止服务
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    public function stop(){
        Db::getInstance()->emptyTable("extend_lock");
    }


    /**
     * +----------------------------------------------------------
     *  服务启动与初始化
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    private function init()
    {
        $fp=fopen(EXTEND_TASKER."tasker_server.lock","a");
        if(!flock($fp,LOCK_EX|LOCK_NB))return;
        //通过文件指针锁定，避免重复拉起服务。
        $this->stop();
        Db::getInstance()->insert(SQL_INSERT_NORMAL)->keyValue(["lock_time"=>time()])->table("extend_lock")->commit();
        do {
            $this->lock(time());//更新锁定时间
            //循环扫描
            Tasker::getInstance()->run();
            Log::debug("Tasker","循环扫描中...");
            sleep(10);
            if($this->isStop()){//间歇10秒后如果发现停止
                Log::debug("Tasker","进程退出...");
                break;
            }
        } while(true);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * +----------------------------------------------------------
     * 更新锁定时间
     * +----------------------------------------------------------
     * @param $time int 锁定时间
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    private function lock($time){
        Db::getInstance()->update()->set(["lock_time"=>$time])->table("extend_lock")->commit();
    }

    /**
     * +----------------------------------------------------------
     *  判断是否停止
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     */
    private function isStop(){
        $data=Db::getInstance()->select()->table("extend_lock")->limit(1)->commit();
        if(empty($data))return true;
        return false;
    }

    /**
     * +----------------------------------------------------------
     *  判断是否锁定
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     */
    private function isLock(){
        $data=Db::getInstance()->select()->table("extend_lock")->limit(1)->commit();
        if(empty($data))return false;
        return (time()-intval($data[0]['lock_time'])<12);
    }
}