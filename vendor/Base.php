<?php

namespace app\vendor;

use app\vendor\lib\Log;

date_default_timezone_set('PRC');
define('FRAME_VERSION', '2.0');

define('APP_CONTROLLER', APP_DIR .  DS . 'controller' . DS);

define('APP_STORAGE', APP_DIR .  DS . 'storage' . DS);
define('APP_TMP', APP_STORAGE.'view'.DS);//渲染完成的视图文件
define('APP_CACHE', APP_STORAGE.'cache'.DS);//缓存文件
define('APP_ROUTE', APP_STORAGE.'route'.DS);//路由缓存文件
define('APP_LOG', APP_STORAGE.'logs'.DS);//日志文件
define('APP_TRASH', APP_STORAGE.'trash'.DS);//垃圾文件

define('APP_CONF',APP_DIR . DS .'config'.DS);
define('APP_LIB', APP_DIR . DS  . 'lib' . DS);
define('APP_VIEW', APP_DIR . DS . 'view' . DS);
define('APP_INNER',APP_DIR.DS.'inner_view'.DS);
define('APP_UI', APP_DIR . DS.'public'. DS  . 'ui' . DS . 'view' . DS);
define('APP_I', APP_DIR . DS . 'public'. DS  .'ui'.DS.'static'. DS);


//载入内置全局函数
require APP_CORE . "Function.php";
// 载入Loader类
require APP_CORE . "Loader.php";
// 注册自动加载
Loader::register();
// 加载配置文件
Config::register();
// 注册错误和异常处理机制
Error::register();
$GLOBALS['start']=microtime(true);
Log::debug("clean",'----------------------------------------------------------------------------------------------');
Log::debug("clean",'Basic loading completed,Framework startup.');





