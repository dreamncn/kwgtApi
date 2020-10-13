<?php
/**
 * File Response.php
 * Author : Dreamn
 * Date : 7/30/2020 12:49 AM
 * Description:响应类
 */

namespace app\vendor\web;
use app\vendor\mvc\Controller;

class Response
{
    /**
     * 获得完整域名（包含协议）
     * @return string
     */
    public static function getAddress()
    {
        return $GLOBALS['http_scheme'] . $_SERVER["HTTP_HOST"];
    }

    /**
     * @return string 获取当前页面的URL
     */
    public static function getNowAddress()
    {
        return $GLOBALS['http_scheme'] . $_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];
    }

    /**
     * 获取本机IP
     * @return string
     */
    public static function getMyIp()
    {
        return gethostbyname(gethostname());
    }

    public static function location($url){
        header("Location:{$url}");
    }

    /**
     * @param false $err 是否错误
     * @param int $code 错误代码（html响应码）
     * @param string $title 错误标题
     * @param string $msg 错误信息
     * @param int $time 跳转延时
     * @param string $url 跳转地址
     * @param string $desc 跳转描述
     */
    public static function msg($err=false,$code=404,$title="",$msg="",$time=3,$url='',$desc="立即跳转"){
        GLOBAL $__module;
        $__module='';
        header("Content-type: text/html; charset=utf-8", true, $code);
        $err=$err?":(":":)";

        if($time==0){
            self::location($url);
            return;
        }
        $data=get_defined_vars();
        $obj=new Controller();
        $obj->setArray($data);
        $obj->setAutoPathDir(APP_INNER.DS."tip");
        if(file_exists(APP_INNER.DS."tip".$code.'.html'))
            $obj->display($code);
        else
            $obj->display('common');
        exit;
    }
}
