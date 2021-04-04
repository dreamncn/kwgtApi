<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\api;

use app\vendor\mvc\Controller;

class BaseController extends Controller
{

    function init()
    {
        header("Content-type: text/html; charset=utf-8");
        //  $this->setLayout("layout");
        $this->setEncode(false);
    }

    //public static function err404(){}
    //public static function err500(){}
}
