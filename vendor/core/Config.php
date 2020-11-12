<?php


namespace app\vendor\core;

use app\vendor\config\Config as ConfigManger;

/*
 * 配置管理
 * */

class Config
{
    static public function register()
    {
        $GLOBALS = [];
        $conf = ConfigManger::getInstance("frame")->get();
        $GLOBALS["frame"] = $conf;
        if (!in_array($_SERVER["HTTP_HOST"], $conf['host'])) {
            if (isDebug()) {
                echo "您的域名绑定错误，当前域名为：{$_SERVER["HTTP_HOST"]} , 请在 /config/frame.yml 第2行添加该域名。";
                exit();
            }
            exit('Something error,please contact to this site administrator.');
        }
        $GLOBALS["route"] = ConfigManger::getInstance("route")->get();

    }
}
