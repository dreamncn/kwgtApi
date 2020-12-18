<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\net_ankio_cc_defense;

use app\vendor\event\EventListener;
define("EXTEND_CC_DEFENSE",APP_EXTEND."net_ankio_cc_defense".DS);

class Main implements EventListener
{
    public function handleEvent($msg)
    {

        (new Ddos())->start();
    }
}
