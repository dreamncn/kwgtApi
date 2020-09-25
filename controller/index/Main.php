<?php
namespace app\controller\index;
use app\vendor\lib\Route;
use app\vendor\lib\Web\Response;

class Main extends BaseController
{
    public function index()
    {
        $this->setData('a','PHPer');
    }
    public function admin(){
        dump(arg('id'));
        dump(Route::url("index",'main','admin',['id'=>'222']));
        dump(Route::url("index",'main','admin',['p'=>'222']));
        dump(Route::url("index",'main','index',['p'=>'222']));
        dump(Route::url("index",'main','index'));
        dump(Route::url("index",'main','api',['id'=>'222','ii'=>123]));
    }
    public function test()
    {
        Response::msg(true,500,"500 不允许访问的页面","Something Worries",5,'/','回到首页');
    }
}
