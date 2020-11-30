<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\admin;

use app\vendor\mvc\Controller;

/**
 * +----------------------------------------------------------
 * Class BaseController
 * +----------------------------------------------------------
 * @package app\controller\admin
 * +----------------------------------------------------------
 * Date: 2020/11/19 12:23 上午
 * Author: ankio
 * +----------------------------------------------------------
 * Desciption:
 * +----------------------------------------------------------
 */
class BaseController extends Controller
{
    public $layout = "";

    function init()
    {
        header("Content-type: text/html; charset=utf-8");
    }

    //public static function err404(){}
    //public static function err500(){}
}
