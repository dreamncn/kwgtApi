<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\vendor\web;

/**
 * +----------------------------------------------------------
 * Class Request
 * +----------------------------------------------------------
 * @package app\vendor\web
 * +----------------------------------------------------------
 * Date: 2020/11/22 11:18 下午
 * Author: ankio
 * +----------------------------------------------------------
 * Desciption:客户端请求处理
 * +----------------------------------------------------------
 */
class Request
{
    /**
     * +----------------------------------------------------------
     * 获取头部信息
     * +----------------------------------------------------------
     * @return array|false
     * +----------------------------------------------------------
     */
    public static function getHeader()
    {
        if (function_exists('getallheaders')) return getallheaders();
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[ucfirst(strtolower(str_replace('_', '-', substr($key, 5))))] = $value;
            }
            if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $header['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
            } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $header['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
            }
            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $header['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
            }
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $header['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
            }
        }
        return $headers;
    }


    public static function getHeaderValue($headName){
        $headers=self::getHeader();
        if(isset($headers[$headName])){
            return $headers[$headName];
        }
        return null;
    }

    /**
     * +----------------------------------------------------------
     * 通过数据库获取浏览器信息，需要配置php.ini https://www.php.net/manual/zh/function.get-browser.php
     * +----------------------------------------------------------
     * @return string
     * +----------------------------------------------------------
     */
    public static function getBroswerByIni(){
        $browser = get_browser($_SERVER['HTTP_USER_AGENT'] ,true);
        return $browser["platform_description"]."（{$browser['browser']})";
    }

    /**
     * +----------------------------------------------------------
     * 获取浏览器信息
     * +----------------------------------------------------------
     * @return string
     * +----------------------------------------------------------
     */
    public static function getBroswer()
    {
        $sys = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif (stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            preg_match("/rv:([\d.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        } else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0] . '(' . $exp[1] . ')';
    }


    /**
     * +----------------------------------------------------------
     * 获取系统信息
     * +----------------------------------------------------------
     * @return string
     * +----------------------------------------------------------
     */
    public static function getOS()
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $os = false;

        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } elseif (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } elseif (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';#添加win10判断
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } elseif (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } elseif (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } elseif (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } elseif (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } elseif (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } elseif (preg_match('/Mac/i', $agent)) {
            $os = 'Mac OS X';
        } elseif (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } elseif (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } elseif (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } elseif (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } elseif (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } elseif (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } elseif (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } elseif (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } elseif (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } elseif (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } elseif (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } elseif (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } else {
            $os = '未知操作系统';
        }
        return $os;
    }


    /**
     * +----------------------------------------------------------
     * 获取客户端真实IP
     * +----------------------------------------------------------
     * @return array|false|mixed|string
     * +----------------------------------------------------------
     */
    public static function getClientIP()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "127.0.0.1"))
            $ip = getenv("HTTP_CLIENT_IP");
        elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "127.0.0.1"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "127.0.0.1"))
            $ip = getenv("REMOTE_ADDR");
        elseif (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown"))
            $ip = $_SERVER["REMOTE_ADDR"];
        else
            $ip = "127.0.0.1";
        return $ip;
    }


    /**
     * +----------------------------------------------------------
     * 是否PJAX请求
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     */
    public static function isPjax()
    {
        return (isset($_SERVER['HTTP_X_PJAX']) && $_SERVER['HTTP_X_PJAX'] == 'true');
    }


    /**
     * +----------------------------------------------------------
     * 是否AJAX请求
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     */
    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }


    /**
     * +----------------------------------------------------------
     * 是否GET请求
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * +----------------------------------------------------------
     * 是否POST请求
     * +----------------------------------------------------------
     * @return bool
     * +----------------------------------------------------------
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}
