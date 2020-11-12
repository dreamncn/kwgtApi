<?php

namespace app\controller\index;

use app\vendor\config\Config;
use app\vendor\mvc\Model;
use app\vendor\web\Response;

class Main extends BaseController
{
    public function index()
    {
        $this->setData('a', 'PHPer');
    }

    public function admin()
    {
        dump('路由生成');
        dump(url('admin', 'main', 'index', ['addr' => 'okkk']));
    }

    public function test()
    {

        Response::msg(true, 403, "你知道什么叫伪静态吗", "You Know?", 10, '/', '回到首页');
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
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => 'okkkk', 'ip' => "12.041232"])->commit();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => 'okkkk', 'ip' => "12.041232"])->commit();

        $data = $sql->select()->commit();
        dump($data);
        $sql->delete()->where(['id' => 1])->commit();
        $data = $sql->select()->commit();
        dump($data);
        $sql->update()->where(['id' => 2])->set(["urls" => "213131213"])->commit();
        $data = $sql->select()->commit();
        dump($data);

        dump($sql->dumpSql());


        dump("transaction");
        $sql->beginTransaction();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => "你是个大傻逼啊啊啊啊"])->commit();
        dump($sql->select()->commit());
        dump($sql->dumpSql());

        $sql->commit();
        dump("commit");
        dump("transaction");
        $sql->beginTransaction();
        $sql->insert(SQL_INSERT_NORMAL)->keyValue(['urls' => "傻逼号"])->commit();
        $sql->update()->set(["ip" => 45456])->where(['urls' => "傻逼号"])->commit();
        dump($sql->select()->commit());
        $sql->rollBack();
        dump("rollBack");
        dump($sql->select()->commit());
    }

    public function config()
    {
        $data = Config::getInstance("config")->get();
        dump($data);
        Config::getInstance("config")->setAll([
            "api_okkk" => 1121211,
            "set" => 222
        ]);
        dump(Config::getInstance("config")->getOne("api_okkk"));
        Config::getInstance("config")->set(
            "api_okkk", ["1" => 233, 'api', "okk" => []]
        );
    }
}
