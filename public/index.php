<?php

use app\vendor\core\Clean;

define('APP_DIR',dirname(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);
define('APP_CORE', APP_DIR . DS . 'vendor' . DS);
require_once(APP_CORE . "core".DS."base.php");

$installFile = APP_DIR.DS."install".DS."index.php";

if(is_file($installFile)&&!is_file(APP_DIR."install".DS."lock") ){
	require_once($installFile);
}else{
    Clean::Run();
}
