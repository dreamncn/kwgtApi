<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

use app\vendor\event\EventManager;
//注册拓展运行位置
EventManager::attach("beforeRunFrame", 'app\extend\net_ankio_cc_defense\Main');

