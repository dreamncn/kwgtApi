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

namespace app\extend\net_ankio_tasker\core;

use app\vendor\database\Sql;
use app\vendor\web\Response;

class Db extends Sql
{
	private static $instance=null;


	public function __construct($tableName = '')
    {
        parent::__construct($tableName);
        $this->setDbLocation(EXTEND_TASKER."data".DS, "db");
        $this->setDatabase("sqlite");

    }

    /**
     * +----------------------------------------------------------
     * 获取对象实例
     * +----------------------------------------------------------
     * @return Db
     * +----------------------------------------------------------
     */
    public static function getInstance(){
        return self::$instance===null?(self::$instance=new Db()):self::$instance;
    }

    public static function InitAsync(){
        self::$instance->execute(
            "CREATE TABLE  IF NOT EXISTS session_record(
                    id integer PRIMARY KEY autoincrement,
                    session varchar(200),
                    count integer,
                    times integer,
                    check_in integer,
                    url text,
                    last_time varchar(200)
                    )"
        );
    }

}