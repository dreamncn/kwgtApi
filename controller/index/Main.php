<?php
namespace app\controller\index;
use app\vendor\web\Response;

class Main extends BaseController
{
    public function index()
    {
        $this->setData('a','PHPer');
    }
    public function admin(){
        dump('路由生成');
        dump(url('admin','main','index',['addr'=>'okkk']));
    }
    public function test()
    {

        Response::msg(true,403,"你知道什么叫伪静态吗","You Know?",10,'/','回到首页');
    }
}
