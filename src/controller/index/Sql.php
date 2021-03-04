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

class Sql extends BaseController
{

    public function init()
    {
        parent::init();

    }

    public function sqlinit()
    {
        dump("该操作应该继承model类，在model/index中执行，此处写法仅用于演示。");
        $sql = new Model("log");
        $sql->setDatabase("sqlite_demo");
        $sql->execute(
            "DROP TABLE IF EXISTS log"
        );
        $sql->execute(
            "CREATE TABLE  IF NOT EXISTS log(
                    id integer PRIMARY KEY autoincrement,
                    urls varchar(200),
                    ip varchar(200))"
        );
        $data = $sql->select()->commit();
        dump("当前数据库数据");
        dump($data);
        $sql->insert(SQL_INSERT_NORMAL)->keyValue([
            'urls' => '1233', 'ip' => "333333",
        ])->commit();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue([
            'urls' => 'okkkk', 'ip' => "12.041232",
        ])->commit();

        $data = $sql->select()->commit();
        dump("执行2次插入操作，当前数据库数据");
        dump($data);
        $sql->delete()->where(['id' => 1])->commit();
        $data = $sql->select()->commit();
        dump("删除id为1的数据，当前数据库数据");
        dump($data);
        $sql->update()->where(['id' => 2])->set(["urls" => "213131213"])
            ->commit();
        $data = $sql->select()->commit();
        dump("更新id为2的数据，当前数据库数据");
        dump($data);
        dump("所有执行过的sql语句以及运行时间");
        dump($sql->dumpSql());


        dump("事务开始");
        $sql->beginTransaction();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => "你是hh"])
            ->commit();
        dump("事务 执行插入 并未提交");
        dump($sql->select()->commit());
        dump("所有执行过的sql语句以及运行时间");
        dump($sql->dumpSql());

        $sql->commit();
        dump("事务提交");
        dump("事务开始");
        $sql->beginTransaction();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => "12222"])->commit();
        $sql->update()->set(["ip" => 45456])->where(['urls' => "大萨达"])
            ->commit();
        dump("事务 执行插入、更新 并未提交");
        dump($sql->select()->commit());
        $sql->rollBack();
        dump("事务 回滚");
        dump($sql->select()->commit());
    }

}
