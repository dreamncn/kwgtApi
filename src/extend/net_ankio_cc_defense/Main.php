<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\net_ankio_cc_defense;

use app\vendor\config\Config;
use app\vendor\event\EventListener;
define("EXTEND_CC_DEFENSE",APP_EXTEND."net_ankio_cc_defense".DS);

class Main implements EventListener
{
    public function handleEvent($msg)
    {

        if(Config::getInstance("config")->setLocation(EXTEND_CC_DEFENSE)->getOne("use"))
            (new Ddos())->start();
    }
}
