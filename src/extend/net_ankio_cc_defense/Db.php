<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * +----------------------------------------------------------
 * File Db
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
use app\vendor\web\Request;

class Db
{
	private static $instance=null;

	private $sql;

	public function __construct() {
		$this->sql=new Sql("ban_ip");
		$this->sql->setDbLocation(APP_EXTEND."net_ankio_cc_defense", "db");

	}
	public static function getInstance(){
		return self::$instance==null?self::$instance=new Db():self::$instance;
	}
	public function add($time){
		$this->sql->beginTransaction();
		try {
			$this->sql->insert(SQL_INSERT_NORMAL)->keyValue(["ip"=>Request::getClientIP(),"expire"=>$time])->commit();
			$this->sql->commit();
		}catch (\Exception $e){
			$this->sql->rollBack();
		}

		$this->clear();
	}
	public function clear(){
		$this->sql->beginTransaction();
		try {
			$this->sql->delete()->where(["expire > time()"])->commit();
			$this->sql->commit();
		}catch (\Exception $e){
			$this->sql->rollBack();
		}
	}
}