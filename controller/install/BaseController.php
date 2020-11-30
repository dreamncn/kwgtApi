<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\install;

use app\vendor\mvc\Controller;

class BaseController extends Controller
{
    public $layout = "";

    function init()
    {
        //header("Content-type: text/html; charset=utf-8");
    }

    //public static function err404(){}
    //public static function err500(){}
}
