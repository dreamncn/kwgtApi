<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\index;

use app\extend\net_ankio_tasker\core\Tasker;
use app\vendor\config\Config;
use app\vendor\debug\Log;
use app\vendor\mvc\Model;
use app\vendor\web\Response;

class Main extends BaseController
{
	public function index()
	{

	}

	public function admin()
	{
        Response::msg(true, 200, "混淆视听3", "收到参数 id=".arg("id",null), -1, '/', '回到首页');
	}

	public function test()
	{
		Response::msg(true, 200, "混淆视听", "收到参数 file=".arg("file",null), -1, '/', '回到首页');
	}

    public function api()
    {
        Response::msg(true, 200, "混淆视听2", "收到参数 id=".arg("id",null), -1, '/', '回到首页');
    }

	public function tasker(){
	    $tasker=Tasker::getInstance();
	    $tasker->add($tasker->cycleNMinute(0),url('index','tasker','tasker_start_1'),"write_0_2",2);
        $tasker->add($tasker->cycleNMinute(1),url('index','tasker','tasker_start_2'),"write_1_-1");
        $tasker->add($tasker->cycleNMinute(2),url('index','tasker','tasker_start_3'),"write_2_3",3);
    }



	public function sqlinit()
	{
		$sql = new Model("log");
		$sql->setDatabase("sqlite");

		$sql->execute(
			"CREATE TABLE  IF NOT EXISTS log(
                    id integer PRIMARY KEY autoincrement,
                    urls varchar(200),
                    ip varchar(200))"
		);
		$sql->emptyTable("log");
		$data = $sql->select()->commit();
		dump($data);
		$sql->insert(SQL_INSERT_NORMAL)->keyValue([
			'urls' => 'okkkk', 'ip' => "12.041232",
		])->commit();
		$sql->insert(SQL_INSERT_NORMAL)->keyValue([
			'urls' => 'okkkk', 'ip' => "12.041232",
		])->commit();

		$data = $sql->select()->commit();
		dump($data);
		$sql->delete()->where(['id' => 1])->commit();
		$data = $sql->select()->commit();
		dump($data);
		$sql->update()->where(['id' => 2])->set(["urls" => "213131213"])
			->commit();
		$data = $sql->select()->commit();
		dump($data);

		dump($sql->dumpSql());


		dump("transaction");
		$sql->beginTransaction();
		$sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => "你是个大傻逼啊啊啊啊"])
			->commit();
		dump($sql->select()->commit());
		dump($sql->dumpSql());

		$sql->commit();
		dump("commit");
		dump("transaction");
		$sql->beginTransaction();
		$sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => "傻逼号"])->commit();
		$sql->update()->set(["ip" => 45456])->where(['urls' => "傻逼号"])
			->commit();
		dump($sql->select()->commit());
		$sql->rollBack();
		dump("rollBack");
		dump($sql->select()->commit());
	}

    public function sqlinit2()
    {
        $sql = new Model("session_record");
        $sql->setDbLocation(APP_EXTEND."net_ankio_cc_defense".DS, "db");
        $sql->setDatabase("sqlite");
        $sql->execute(
            "CREATE TABLE  IF NOT EXISTS session_record(
                    id integer PRIMARY KEY autoincrement,
                    session varchar(200),
                    count integer
                    )"
        );
        $sql->emptyTable("session_record");
        $data = $sql->select()->commit();
        dump($data);
        $sql->insert(SQL_INSERT_NORMAL)->keyValue([
            'session' => '0000000', 'count' => 1,
        ])->commit();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue([
            'session' => '14227272', 'count' => 1,
        ])->commit();

        $data = $sql->select()->commit();
        dump($data);
        $sql->delete()->where(['id' => 1])->commit();
        $data = $sql->select()->commit();
        dump($data);
        $sql->update()->where(['id' => 2])->set(["session" => "0212100"])
            ->commit();
        $data = $sql->select()->commit();
        dump($data);

        dump($sql->dumpSql());


        dump("transaction");
        $sql->beginTransaction();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['session' => "你是个大傻逼啊啊啊啊"])
            ->commit();
        dump($sql->select()->commit());
        dump($sql->dumpSql());

        $sql->commit();
        dump("commit");
        dump("transaction");
        $sql->beginTransaction();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['session' => "傻逼号"])->commit();
        $sql->update()->set(["id" => 1])->where(['session' => "傻逼号"])
            ->commit();
        dump($sql->select()->commit());
        $sql->rollBack();
        dump("rollBack");
        dump($sql->select()->commit());
    }
	public function config()
	{
		$data = Config::getInstance("db")->get();
		dump($data);
		Config::getInstance("config")->setAll([
			"api_okkk" => 1121211,
			"set"      => 222,
		]);
		dump(Config::getInstance("config")->getOne("api_okkk"));
		Config::getInstance("config")->set(
			"api_okkk", ["1" => 233, 'api', "okk" => []]
		);
	}
}
