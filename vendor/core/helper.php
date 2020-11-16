<?php


use app\vendor\debug\Dump;
use app\vendor\web\Route;


/*数据库常量*/
define('SQL_INSERT_NORMAL', 0);
define('SQL_INSERT_IGNORE', 1);
define('SQL_INSERT_DUPLICATE', 2);



function url($m = 'index', $c = 'main', $a = 'index', $param = array())
{
    return Route::url(...func_get_args());
}


/**
 * @param null $var 需要输出的变量
 * @param bool $exit 是否退出
 */

function dump($var, $exit = false)
{
    if (isConsole()) {
        $line = debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'];
        echo $line . "\n";
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
 * @param bool $trim
 * @return mixed|string|null
 */

function arg($name = null, $default = null, $trim = true)
{
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
function isDebug()
{
    return isset($GLOBALS["frame"]['debug']) && $GLOBALS["frame"]['debug'];
}

/**
 * 判断当前是否为命令行状态
 * @return bool
 */
function isConsole()
{

    return isset($_SERVER['CLEAN_CONSOLE']) && $_SERVER['CLEAN_CONSOLE'];
}

/**
 * 取随机字符串
 * @param int $length 字符串长度
 * @param bool $upper 是否包含大写字母
 * @param bool $lower 是否包含小写字母
 * @param bool $number 是否包含数字
 * @return string
 */
function getRandom($length = 8,$upper=true,$lower=true,$number=true)
{
    $charsList=[
        'abcdefghijklmnopqrstuvwxyz',
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '0123456789'
    ];
    $chars="";
    if($upper)$chars.=$charsList[0];
    if($lower)$chars.=$charsList[1];
    if($number)$chars.=$charsList[2];
    if($chars==="")$chars=$charsList[2];
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

