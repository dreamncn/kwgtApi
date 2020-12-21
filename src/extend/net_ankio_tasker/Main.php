<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\net_ankio_tasker;

use app\vendor\config\Config;
use app\vendor\event\EventListener;
define("EXTEND_TASKER",APP_EXTEND."net_ankio_tasker".DS);

class Main implements EventListener
{
    public function handleEvent($event,$msg)
    {

    }
}
