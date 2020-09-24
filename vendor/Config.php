<?php
namespace app\vendor;
/*
 * 配置管理
 * */
class Config
{
    static public function register()
    {
        $GLOBALS=[];
        $conf = include_once(APP_CONF . 'Frame.php');
        $GLOBALS["frame"]=$conf;
        if (!in_array($_SERVER["HTTP_HOST"], $conf['host'])) {
            if(isDebug()){
                echo "您的域名绑定错误，当前域名为：{$_SERVER["HTTP_HOST"]} , 请在 /config/Frame.php 第六行添加该域名。";
                exit();
            }
            exit('Something error,please contact to this site administrator.');
        }
        $GLOBALS["database"]= include_once(APP_CONF . 'Database.php');
        $GLOBALS["route"]= include_once(APP_CONF . 'Route.php');

    }
}
