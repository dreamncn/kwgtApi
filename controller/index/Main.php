<?php
namespace app\controller\index;
use app\vendor\lib\Web\Response;

class Main extends BaseController
{
    public function index()
    {
        //echo 'hello,clean-php!';
    }
    public function test()
    {
        Response::msg(true,500,"500 不允许访问的页面","Something Worries",5,'/','回到首页');
    }
}
