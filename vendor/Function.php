<?php


use app\vendor\Clean;
use app\vendor\lib\Dump;
use app\vendor\lib\Route;

function url($m='index', $c = 'main', $a = 'index', $param = array())
{
    if(is_array($m))
        $param=$m;
    if (isset($param['m'])) {
        $m = $param['m'];
        unset($param['m']);
    }
    if (isset($param['c'])) {
        $c = $param['c'];
        unset($param['c']);
    }
    if (isset($param['a'])) {
        $a = $param['a'];
        unset($param['a']);
    }

    $params = empty($param) ? '' : '&' . http_build_query($param);
    $route = "$m/$c/$a";
    $url = $_SERVER["SCRIPT_NAME"] . "?m=$m&c=$c&a=$a$params";
    Cache::init(365 * 24 * 60 * 60, APP_ROUTE);
    //初始化路由缓存，不区分大小写
    $data = Cache::get('route'.$url);
    if($data!==null)return  $data;

    if (!empty($GLOBALS['rewrite'])) {
        if (!isset($GLOBALS['url_array_instances'][$url])) {
            foreach ($GLOBALS['rewrite'] as $rule => $mapper) {
                $mapper = '/^' . str_ireplace(array('/', '<a>', '<c>', '<m>'), array('\/', '(?P<a>\w+)', '(?P<c>\w+)', '(?P<m>\w+)'), $mapper) . '/i';
                if (preg_match($mapper, $route, $matchs)) {
                    $rule = str_ireplace(array('<a>', '<c>', '<m>'), array($a, $c, $m), $rule);
                    $match_param_count = 0;
                    $param_in_rule = substr_count($rule, '<');
                    if (!empty($param) && $param_in_rule > 0) {
                        foreach ($param as $param_key => $param_v) {
                            if (false !== stripos($rule, '<' . $param_key . '>')) $match_param_count++;
                        }
                    }
                    if ($param_in_rule == $match_param_count) {
                        $GLOBALS['url_array_instances'][$url] = $rule;
                        if (!empty($param)) {
                            $_args = array();
                            foreach ($param as $arg_key => $arg) {
                                $count = 0;
                                $GLOBALS['url_array_instances'][$url] = str_ireplace('<' . $arg_key . '>', $arg, $GLOBALS['url_array_instances'][$url], $count);
                                if (!$count) $_args[$arg_key] = $arg;
                            }
                            $GLOBALS['url_array_instances'][$url] = preg_replace('/<\w+>/', '', $GLOBALS['url_array_instances'][$url]) . (!empty($_args) ? '?' . http_build_query($_args) : '');
                        }

                        if (0 !== stripos($GLOBALS['url_array_instances'][$url], $GLOBALS['http_scheme'])) {
                            $GLOBALS['url_array_instances'][$url] = $GLOBALS['http_scheme'] . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') . '/' . $GLOBALS['url_array_instances'][$url];
                        }
                        Cache::set('route'.$url,$GLOBALS['url_array_instances'][$url]);
                        return $GLOBALS['url_array_instances'][$url];
                    }
                }
            }
            return isset($GLOBALS['url_array_instances'][$url]) ? $GLOBALS['url_array_instances'][$url] : $url;
        }
        return $GLOBALS['url_array_instances'][$url];
    }
    return $url;
}



/**
 * @param null $var 需要输出的变量
 * @param bool $exit 是否退出
 */

function dump($var, $exit = false)
{
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

