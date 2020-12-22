<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\net_ankio_tasker;
use app\vendor\debug\Log;
use app\vendor\web\Request;

/**
 * +----------------------------------------------------------
 * Class Async
 * +----------------------------------------------------------
 * @package app\extend\net_ankio_tasker
 * +----------------------------------------------------------
 * Date: 2020/12/20 23:04
 * Author: ankio
 * +----------------------------------------------------------
 * Desciption:异步处理，多用于后台与多线程
 * +----------------------------------------------------------
 */
class Async
{
    static private $err = '';

    public static function err()
    {
        return self::$err;
    }

    /**
     * 发起异步请求，就是后台服务请求
     *
     * @param        $url      string 完整的URL
     * @param string $method   调用的方法
     * @param array  $data     传递的数据
     * @param array  $cookie   cookie数组
     * @param string $identify 唯一标识符
     *
     * @return bool
     */
    public static function request($url, $method = 'GET', $data = [], $cookie = [], $identify = 'clean')
    {

        $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
        $port = $url_array['scheme'] == 'http' ? 80 : 443;
        $fp = fsockopen(($url_array['scheme'] == 'http' ? "" : 'ssl://') . $url_array['host'], $port, $errno, $errstr, 30);
        if (!$fp) {
            self::$err = '无法向该URL发起请求' . $errstr;
            if (isDebug()) {
                Log::warn('Async', '异步发起失败，原因：' . self::$err);
            }
            return false;
        }
        if ($method == 'GET')
            $getPath = $url_array['path'] . "?" . http_build_query($data);
        else
            $getPath = $url_array['path'];

        $header = $method . " " . $getPath;
        $header .= " HTTP/1.1" . PHP_EOL;
        $header .= "Host: " . $url_array['host'] . "" . PHP_EOL; //HTTP 1.1 Host域不能省略
        $token = getRandom(128);

        file_put_contents(APP_TRASH . md5(md5($token . $identify)), json_encode(['token' => $token, 'timeout' => time() + 60]));

        $header .= "Token: " . md5($token) . "" . PHP_EOL;
        $header .= "Identify: " . md5($token . $identify) . "" . PHP_EOL;
        $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 " . PHP_EOL;
        $header .= "Connection:Close" . PHP_EOL;
        if (!empty($cookie)) {
            $_cookie = strval(null);
            foreach ($cookie as $k => $v) {
                $_cookie .= $k . "=" . $v . "; ";
            }
            $cookie_str = "Cookie: " . $_cookie . " " . PHP_EOL;//传递Cookie
            $header .= $cookie_str;
        }

        if (!empty($data)) {
            $_post = "" . PHP_EOL . http_build_query($data);
            $post_str = "Content-Type: application/x-www-form-urlencoded" . PHP_EOL;//POST数据
            $post_str .= "Content-Length: " . strlen($_post) . " " . PHP_EOL;//POST数据的长度
            $post_str .= $_post . PHP_EOL . PHP_EOL . " "; //传递POST数据
            $header .= $post_str;
        }
        fwrite($fp, $header);
        fclose($fp);
        return true;
    }

    /**
     * 响应后台异步请求
     *
     * @param int $time 最大运行时间
     */
    public static function response($time = 0)
    {
        if (!self::checkToken()) {
            if (isDebug()) {
                Log::warn('Sync', '异步响应失败，原因：' . self::$err);
            }
            exit;
        }
        ignore_user_abort(true); // 后台运行，不受前端断开连接影响
        set_time_limit($time);
        ob_end_clean();
        header("Connection: close");
        header("HTTP/1.1 200 OK");
        ob_start();
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();//输出当前缓冲
        flush();
        if (function_exists("fastcgi_finish_request")) {
            fastcgi_finish_request(); /* 响应完成, 关闭连接 */
        }
        sleep(1);

    }

    private static function checkToken()
    {
        $header = Request::getHeader();
        if (isset($header['Token']) && isset($header['Identify'])) {
            $file = APP_TRASH . md5($header['Identify']);
            if (!file_exists($file)) {
                self::$err = 'token不存在';
                return false;
            }
            $token = json_decode(trim(file_get_contents($file)), true);
            unlink($file);
            if ($token && isset($token['timeout']) && isset($token['token'])) {
                if (intval($token['timeout']) < time()) {
                    self::$err = '响应超时';
                    return false;
                }
                if ($header['Token'] !== md5($token['token'])) {
                    self::$err = 'token校验失败';
                    return false;
                }
                return true;
            }
        }
        return false;
    }
}
