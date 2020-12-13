<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/
namespace app\extend\net_ankio_cc_defense\core;
use app\vendor\web\Session;

class Ddos
{
    public function start(){
        Session::getInstance()->start();
    }

    private function checkTime(){

    }
}