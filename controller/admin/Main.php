<?php
namespace app\controller\admin;
use app\vendor\debug\Route;
use app\vendor\web\Response;

class Main extends BaseController
{
    public function index()
    {
        echo 'Hi,Admin';
    }
    public function admin(){
        dump('Admin路由生成');
        dump(url('admin','main','index',['addr'=>'123']));
    }
    public function test()
    {

        Response::msg(true,403,"你知道什么叫伪静态吗","You Know?",10,'/','回到首页');
    }
}
