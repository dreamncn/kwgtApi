<?php


use app\vendor\debug\Dump;
use app\vendor\web\Route;

function url($m='index', $c = 'main', $a = 'index', $param = array())
{
    return Route::url(...func_get_args());
}



/**
 * @param null $var 需要输出的变量
 * @param bool $exit 是否退出
 */

function dump($var, $exit = false)
{
    if(isConsole()){
        $line = debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'];
        echo $line."\n";
        var_dump($var);
        if ($exit) exit;
        return;
    }
    $line = debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'];
    echo <<<EOF
<style>pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div style="text-align: left">
<pre class="xdebug-var-dump" dir="ltr"><small>{$line}</small>\r\n
EOF;

    $dump = new Dump();
    $dump->dumpType($var);

    echo '</pre></div>';


    if ($exit) exit;
}

/**
 * @param null $name
 * @param null $default
 * @param bool $trim 移除字符串两侧的空白字符或其他预定义字符
 * @param null $filter
 * @return mixed|string|null
 */

function arg($name = null, $default = null, $trim = false, $filter = null)
{
    switch ($filter) {
        case Route::Get:
            $_REQUEST = $_GET;
            break;
        case Route::Post:
            $_REQUEST = $_POST;
            break;
        case Route::Cookie:
            $_REQUEST = $_COOKIE;
            break;
        default:
    }
    if (!isset($_REQUEST['m'])) $_REQUEST['m'] = 'index';
    if ($name) {
        if (!isset($_REQUEST[$name])) return $default;
        $arg = $_REQUEST[$name];
        if ($trim) $arg = trim($arg);
    } else {
        $arg = $_REQUEST;
    }
    return $arg;
}


/**
 * 检查编码
 * @param $string string 编码类型
 * @return string
 */
function chkCode($string)
{
    $encode = mb_detect_encoding($string, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
    return mb_convert_encoding($string, 'UTF-8', $encode);
}



/**
 * 判断当前是否为调试状态
 * @return bool
 */
function isDebug(){
    return isset($GLOBALS["frame"]['debug'])&&$GLOBALS["frame"]['debug'];
}
/**
 * 判断当前是否为命令行状态
 * @return bool
 */
function isConsole(){

    return isset($_SERVER['CLEAN_CONSOLE'])&&$_SERVER['CLEAN_CONSOLE'];
}
/**
 * 取随机字符串
 * @param int $length
 * @return string
 */
function getRandom($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

