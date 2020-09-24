<?php
namespace app\controller\index;
use app\vendor\mvc\Controller;

class BaseController extends Controller
{
    public string $layout = "layout";

    function init()
    {
        header("Content-type: text/html; charset=utf-8");
    }

    public static function err404(){

    }
}
