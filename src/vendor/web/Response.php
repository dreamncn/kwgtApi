<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File Response.php
 * Author : Dreamn
 * Date : 7/30/2020 12:49 AM
 * Description:响应类
 */

namespace app\vendor\web;

use app\vendor\debug\Log;
use app\vendor\mvc\Controller;

/**
 * +----------------------------------------------------------
 * Class Response
 * +----------------------------------------------------------
 * @package app\vendor\web
 * +----------------------------------------------------------
 * Date: 2020/11/22 11:21 下午
 * Author: ankio
 * +----------------------------------------------------------
 * Desciption:客户端响应类
 * +----------------------------------------------------------
 */
class Response
{

	/**
	 * +----------------------------------------------------------
	 * 获取当前访问的URL域名
	 * +----------------------------------------------------------
	 * @return string
	 * +----------------------------------------------------------
	 */
	public static function getAddress()
    {
        return $GLOBALS['http_scheme'] . $_SERVER["HTTP_HOST"];
    }

	/**
	 * +----------------------------------------------------------
	 * 获取当前访问的地址
	 * +----------------------------------------------------------
	 * @return string
	 * +----------------------------------------------------------
	 */
	public static function getNowAddress()
    {
        return $GLOBALS['http_scheme'] . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
    }

	/**
	 * +----------------------------------------------------------
	 * 获取当前服务器IP
	 * +----------------------------------------------------------
	 */
    public static function getMyIp()
    {
        return gethostbyname(gethostname());
    }


	/**
	 * +----------------------------------------------------------
	 * 跳转提示类
	 * +----------------------------------------------------------
	 * @param  false   $err 是否错误
	 * @param  int     $code 错误代码（200、403、404等）
	 * @param  string  $title 错误标题
	 * @param  string  $msg 错误信息
	 * @param  int     $time 跳转时间
	 * @param  string  $url 跳转URL
	 * @param  string  $desc 跳转描述
	 * +----------------------------------------------------------
	 */
	public static function msg($err = false, $code = 404, $title = "", $msg = "", $time = 3, $url = '', $desc = "立即跳转")
    {
        global $__module;
        $__module = '';
        header("Content-type: text/html; charset=utf-8", true, $code);
        $err = $err ? ":(" : ":)";

        if ($time == 0) {
            self::location($url);
            return;
        }
        $data = get_defined_vars();
        $obj = new Controller();
        $obj->setArray($data);
        $obj->setAutoPathDir(APP_INNER . DS . "tip");
        if (file_exists(APP_INNER . DS . "tip" . $code . '.html'))
            $obj->display($code);
        else
            $obj->display('common');
	    Log::debug('Clean', '出现重定向或不可访问的页面。响应代码：'.$code );
	    Log::debug('Clean', '退出框架，总耗时: ' . strval((microtime(true) - $GLOBALS['frame_start']) * 1000) . 'ms');
	    exit;
    }

	/**
	 * +----------------------------------------------------------
	 * 直接跳转
	 * +----------------------------------------------------------
	 * @param $url
	 * +----------------------------------------------------------
	 */
	public static function location($url)
    {
        header("Location:{$url}");
    }
}
