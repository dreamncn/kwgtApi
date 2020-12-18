<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * +----------------------------------------------------------
 * File Record
 * +----------------------------------------------------------
 * @package app\extend\net_ankio_cc_defense
 * +----------------------------------------------------------
 * Date: 2020/12/4 12:04 上午
 * Author: ankio
 * +----------------------------------------------------------
 * Desciption: 本地数据库操作
 * +----------------------------------------------------------
 */

namespace app\extend\net_ankio_cc_defense;

use app\vendor\database\Sql;

class Record
{
	private static $instance=null;

	private $sql;

	public function __construct() {
		$this->sql=new Sql("session_record");
		$this->sql->setDbLocation(EXTEND_CC_DEFENSE, "db");
		$this->sql->setDatabase("sqlite");
        $this->sql->execute(
            "CREATE TABLE  IF NOT EXISTS session_record(
                    id integer PRIMARY KEY autoincrement,
                    session varchar(200),
                    count integer,
                    times integer,
                    check_in integer,
                    last_time varchar(200)
                    )"
        );
	}

    /**
     * +----------------------------------------------------------
     * 获取对象实例
     * +----------------------------------------------------------
     * @return Record
     * +----------------------------------------------------------
     */
	public static function getInstance(){
		return self::$instance===null?(self::$instance=new Record()):self::$instance;
	}

    /**
     * +----------------------------------------------------------
     * 添加一条记录
     * +----------------------------------------------------------
     * * @param $id
     * @param $count
     * @param $times
     * @param $last_time
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
	public function add($id,$count,$times,$last_time){

        $this->sql
            ->insert(SQL_INSERT_NORMAL)
            ->keyValue(
                ["session"=>$id,"count"=>$count,"times"=>$times,"last_time"=>$last_time]
            )->commit();

		$this->clear();
	}

    /**
     * +----------------------------------------------------------
     * 更新修改记录
     * +----------------------------------------------------------
     * * @param $id
     * @param $data
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
	public function update($id,$data){
        $this->sql->update()->where(["session"=>$id])->set($data)->commit();
	}

    /**
     * +----------------------------------------------------------
     * 获取记录
     * +----------------------------------------------------------
     * * @param $session
     * +----------------------------------------------------------
     * @return array
     * +----------------------------------------------------------
     */
    public function get($session){
        $data=$this->sql->select("*")
            ->where(["session"=>$session])
            ->limit("1")
            ->commit();
        if(!empty($data))return $data[0];
        return null;
    }
    /**
     * +----------------------------------------------------------
     * 清理缓存
     * +----------------------------------------------------------
     * @return void
     * +----------------------------------------------------------
     */
    public function clear(){
        $this->sql->delete()->where(["last_time > :time",":time"=>time()+60*60*24])->commit();
    }
}